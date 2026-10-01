<?php
/**
 * Two-step sign-in (TOTP) — Google Authenticator, Microsoft Authenticator,
 * 1Password, Authy… Each person turns it on in their own profile.
 *
 * Secrets are encrypted at rest (libsodium), codes can't be replayed,
 * recovery codes are hashed and single-use, the second step is rate-limited.
 * Emergency off-switch: define( 'QAF_NO_2FA', true ); in wp-config.php.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

/**
 * Feature available?
 *
 * @return bool
 */
function qaf_2fa_available() {
	return ! ( defined( 'QAF_NO_2FA' ) && QAF_NO_2FA ) && qaf_opt( 'twofa_enable', 1 );
}

/* ================================================= Crypto helpers */

/**
 * Base32 (RFC 4648) encode.
 *
 * @param string $bin Bytes.
 * @return string
 */
function qaf_2fa_b32enc( $bin ) {
	$a    = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
	$bits = '';
	foreach ( str_split( $bin ) as $c ) {
		$bits .= str_pad( decbin( ord( $c ) ), 8, '0', STR_PAD_LEFT );
	}
	$out = '';
	foreach ( str_split( $bits, 5 ) as $chunk ) {
		$out .= $a[ bindec( str_pad( $chunk, 5, '0' ) ) ];
	}
	return $out;
}

/**
 * Base32 decode.
 *
 * @param string $s Text.
 * @return string
 */
function qaf_2fa_b32dec( $s ) {
	$a    = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
	$s    = strtoupper( preg_replace( '/[^A-Za-z2-7]/', '', (string) $s ) );
	$bits = '';
	foreach ( str_split( $s ) as $c ) {
		$bits .= str_pad( decbin( strpos( $a, $c ) ), 5, '0', STR_PAD_LEFT );
	}
	$out = '';
	foreach ( str_split( $bits, 8 ) as $byte ) {
		if ( 8 === strlen( $byte ) ) {
			$out .= chr( bindec( $byte ) );
		}
	}
	return $out;
}

/**
 * Encryption key derived from the site's secret keys.
 *
 * @return string 32 bytes.
 */
function qaf_2fa_key() {
	return hash( 'sha256', wp_salt( 'secure_auth' ) . '|qaf-2fa', true );
}

/**
 * Encrypt a secret for storage.
 *
 * @param string $plain Secret.
 * @return string
 */
function qaf_2fa_seal( $plain ) {
	if ( function_exists( 'sodium_crypto_secretbox' ) ) {
		$nonce = random_bytes( SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
		return 'enc:' . base64_encode( $nonce . sodium_crypto_secretbox( $plain, $nonce, qaf_2fa_key() ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
	}
	return 'raw:' . $plain;
}

/**
 * Decrypt a stored secret.
 *
 * @param string $stored Stored value.
 * @return string
 */
function qaf_2fa_open( $stored ) {
	$stored = (string) $stored;
	if ( 0 === strpos( $stored, 'enc:' ) && function_exists( 'sodium_crypto_secretbox_open' ) ) {
		$raw = base64_decode( substr( $stored, 4 ), true ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
		if ( false === $raw || strlen( $raw ) <= SODIUM_CRYPTO_SECRETBOX_NONCEBYTES ) {
			return '';
		}
		$out = sodium_crypto_secretbox_open( substr( $raw, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES ), substr( $raw, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES ), qaf_2fa_key() );
		return false === $out ? '' : $out;
	}
	return 0 === strpos( $stored, 'raw:' ) ? substr( $stored, 4 ) : '';
}

/**
 * TOTP code for a time step (RFC 6238, SHA-1, 6 digits, 30 s).
 *
 * @param string $secret Base32 secret.
 * @param int    $step   Time step.
 * @return string
 */
function qaf_2fa_code( $secret, $step ) {
	$h = hash_hmac( 'sha1', pack( 'N2', 0, $step ), qaf_2fa_b32dec( $secret ), true );
	$o = ord( $h[19] ) & 0xf;
	$v = ( ( ord( $h[ $o ] ) & 0x7f ) << 24 ) | ( ord( $h[ $o + 1 ] ) << 16 ) | ( ord( $h[ $o + 2 ] ) << 8 ) | ord( $h[ $o + 3 ] );
	return str_pad( (string) ( $v % 1000000 ), 6, '0', STR_PAD_LEFT );
}

/**
 * Check a 6-digit code (±30 s drift, no replay).
 *
 * @param string $secret  Secret.
 * @param string $code    Code.
 * @param int    $user_id User (for replay protection; 0 = setup).
 * @return bool
 */
function qaf_2fa_verify_code( $secret, $code, $user_id = 0 ) {
	$code = preg_replace( '/\D/', '', (string) $code );
	if ( 6 !== strlen( $code ) || ! $secret ) {
		return false;
	}
	$now  = (int) floor( time() / 30 );
	$last = $user_id ? (int) get_user_meta( $user_id, '_qaf_2fa_last', true ) : 0;
	foreach ( array( 0, -1, 1 ) as $d ) {
		if ( hash_equals( qaf_2fa_code( $secret, $now + $d ), $code ) ) {
			if ( $user_id ) {
				if ( $now + $d <= $last ) {
					return false; // Already used.
				}
				update_user_meta( $user_id, '_qaf_2fa_last', $now + $d );
			}
			return true;
		}
	}
	return false;
}

/* ================================================= Per-user state */

/**
 * Is 2FA on for this user?
 *
 * @param int $user_id User.
 * @return bool
 */
function qaf_2fa_enabled( $user_id ) {
	return qaf_2fa_available() && '' !== (string) get_user_meta( $user_id, '_qaf_2fa_secret', true );
}

/**
 * The user's secret.
 *
 * @param int $user_id User.
 * @return string
 */
function qaf_2fa_secret( $user_id ) {
	return qaf_2fa_open( get_user_meta( $user_id, '_qaf_2fa_secret', true ) );
}

/**
 * Hash for a recovery code.
 *
 * @param string $code Code.
 * @return string
 */
function qaf_2fa_hash_recovery( $code ) {
	return hash_hmac( 'sha256', strtolower( preg_replace( '/[^a-z0-9]/i', '', $code ) ), wp_salt( 'auth' ) . '|qaf-2fa-rc' );
}

/**
 * Make 8 fresh recovery codes (returns the plain codes once).
 *
 * @param int $user_id User.
 * @return string[]
 */
function qaf_2fa_new_recovery( $user_id ) {
	$codes  = array();
	$hashes = array();
	for ( $i = 0; $i < 8; $i++ ) {
		$c        = strtolower( qaf_2fa_b32enc( random_bytes( 7 ) ) );
		$c        = substr( $c, 0, 5 ) . '-' . substr( $c, 5, 5 );
		$codes[]  = $c;
		$hashes[] = qaf_2fa_hash_recovery( $c );
	}
	update_user_meta( $user_id, '_qaf_2fa_recovery', $hashes );
	return $codes;
}

/**
 * Use a recovery code.
 *
 * @param int    $user_id User.
 * @param string $code    Code.
 * @return bool
 */
function qaf_2fa_use_recovery( $user_id, $code ) {
	$list = (array) get_user_meta( $user_id, '_qaf_2fa_recovery', true );
	$hash = qaf_2fa_hash_recovery( $code );
	foreach ( $list as $i => $h ) {
		if ( is_string( $h ) && hash_equals( $h, $hash ) ) {
			unset( $list[ $i ] );
			update_user_meta( $user_id, '_qaf_2fa_recovery', array_values( $list ) );
			return true;
		}
	}
	return false;
}

/* ================================================= Sign-in: step two */

/**
 * After a correct password: stop, and ask for the code.
 *
 * @param WP_User|WP_Error|null $user User.
 * @return WP_User|WP_Error|null
 */
function qaf_2fa_intercept( $user ) {
	// phpcs:disable WordPress.Security.NonceVerification.Missing
	$form = ( did_action( 'login_init' ) && isset( $_POST['log'] ) ) || isset( $_POST['woocommerce-login-nonce'] );
	if ( ! $form || ! ( $user instanceof WP_User ) || ! qaf_2fa_enabled( $user->ID ) ) {
		return $user;
	}
	$token = wp_generate_password( 40, false );
	set_transient(
		'qaf2fa_' . hash( 'sha256', $token ),
		array(
			'uid'      => $user->ID,
			'remember' => ! empty( $_POST['rememberme'] ),
			'redirect' => isset( $_REQUEST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_REQUEST['redirect_to'] ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			'tries'    => 0,
		),
		10 * MINUTE_IN_SECONDS
	);
	// phpcs:enable
	wp_safe_redirect( add_query_arg( array( 'action' => 'qaf_2fa', 'qaf2fa' => rawurlencode( $token ) ), wp_login_url() ) );
	exit;
}
add_filter( 'authenticate', 'qaf_2fa_intercept', 99 );

/**
 * The code screen (wp-login.php?action=qaf_2fa).
 */
function qaf_2fa_screen() {
	if ( ! qaf_2fa_available() ) {
		wp_safe_redirect( wp_login_url() );
		exit;
	}
	nocache_headers();
	$token = isset( $_REQUEST['qaf2fa'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['qaf2fa'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- the token is the proof.
	$key   = 'qaf2fa_' . hash( 'sha256', $token );
	$data  = $token ? get_transient( $key ) : false;
	if ( ! $data ) {
		wp_safe_redirect( add_query_arg( 'qaf2fa_expired', 1, wp_login_url() ) );
		exit;
	}
	$user  = get_userdata( (int) $data['uid'] );
	$error = '';

	if ( 'POST' === ( $_SERVER['REQUEST_METHOD'] ?? '' ) && $user ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$code = isset( $_POST['qaf_2fa_code'] ) ? sanitize_text_field( wp_unslash( $_POST['qaf_2fa_code'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$ok   = preg_match( '/[a-z]/i', $code ) ? qaf_2fa_use_recovery( $user->ID, $code ) : qaf_2fa_verify_code( qaf_2fa_secret( $user->ID ), $code, $user->ID );
		if ( $ok ) {
			delete_transient( $key );
			wp_set_auth_cookie( $user->ID, ! empty( $data['remember'] ) );
			do_action( 'wp_login', $user->user_login, $user ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- core hook.
			$to = apply_filters( 'login_redirect', $data['redirect'] ? $data['redirect'] : admin_url(), $data['redirect'], $user ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- core hook.
			wp_safe_redirect( $to ? $to : admin_url() );
			exit;
		}
		++$data['tries'];
		do_action( 'wp_login_failed', $user->user_login, new WP_Error( 'qaf_2fa' ) ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- core hook.
		if ( $data['tries'] >= 5 ) {
			delete_transient( $key );
			wp_safe_redirect( add_query_arg( 'qaf2fa_expired', 1, wp_login_url() ) );
			exit;
		}
		set_transient( $key, $data, 10 * MINUTE_IN_SECONDS );
		$error = __( 'الرمز غير صحيح. تأكد من وقت هاتفك وجرّب أحدث رمز.', 'qafesheh' );
	}

	login_header( __( 'التحقق بخطوتين', 'qafesheh' ), '', $error ? new WP_Error( 'qaf_2fa', $error ) : null );
	?>
	<form name="qaf2faform" id="loginform" class="qaf-2fa-form" action="<?php echo esc_url( add_query_arg( array( 'action' => 'qaf_2fa', 'qaf2fa' => rawurlencode( $token ) ), wp_login_url() ) ); ?>" method="post" autocomplete="off">
		<p class="qaf-2fa-intro"><span class="qaf-2fa-ic" aria-hidden="true"><svg viewBox="0 0 24 24" width="22" height="22"><rect x="6.5" y="2.5" width="11" height="19" rx="2.5" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M10 18.2h4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><path d="m9.3 10.6 1.9 1.9 3.6-3.8" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span><?php esc_html_e( 'افتح تطبيق المصادقة واكتب الرمز المكوّن من ٦ أرقام لموقع مجموعة قفيشة.', 'qafesheh' ); ?></p>
		<p>
			<label for="qaf_2fa_code"><?php esc_html_e( 'رمز الدخول', 'qafesheh' ); ?></label>
			<input type="text" name="qaf_2fa_code" id="qaf_2fa_code" class="input qaf-2fa-code" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9A-Za-z\- ]*" maxlength="14" placeholder="123 456" required autofocus>
		</p>
		<p class="qaf-2fa-hint"><?php esc_html_e( 'أضعت هاتفك؟ اكتب أحد رموز الاسترداد بدلاً منه (مثل abcde-12345).', 'qafesheh' ); ?></p>
		<p class="submit"><input type="submit" class="button button-primary button-large" value="<?php esc_attr_e( 'تحقّق وادخل', 'qafesheh' ); ?>"></p>
	</form>
	<p id="nav"><a href="<?php echo esc_url( wp_login_url() ); ?>">← <?php esc_html_e( 'الدخول بحساب آخر', 'qafesheh' ); ?></a></p>
	<style>
		.qaf-2fa-intro{display:flex;gap:12px;align-items:flex-start;margin:0 0 18px!important;font-size:14px;line-height:1.55;color:#45524F}
		.qaf-2fa-ic{flex:none;display:grid;place-items:center;width:40px;height:40px;border-radius:12px;background:#0F0E0C;color:#D9B85C}
		.qaf-2fa-code{font-size:26px!important;letter-spacing:.3em;text-align:center;font-variant-numeric:tabular-nums}
		.qaf-2fa-hint{font-size:12px;color:#6B7673;margin:-6px 0 16px!important}
	</style>
	<?php
	login_footer( 'qaf_2fa_code' );
	exit;
}
add_action( 'login_form_qaf_2fa', 'qaf_2fa_screen' );

add_filter(
	'wp_login_errors',
	static function ( $errors ) {
		if ( isset( $_GET['qaf2fa_expired'] ) && $errors instanceof WP_Error ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$errors->add( 'qaf_2fa_expired', __( 'لحمايتك انتهت صلاحية محاولة الدخول. سجّل الدخول مرة ثانية.', 'qafesheh' ) );
		}
		return $errors;
	}
);

/* ================================================= Profile: set up / manage */

/**
 * Profile section.
 *
 * @param WP_User $user User being edited.
 */
function qaf_2fa_profile( $user ) {
	if ( ! qaf_2fa_available() ) {
		return;
	}
	$own = get_current_user_id() === $user->ID;
	$on  = qaf_2fa_enabled( $user->ID );
	echo '<h2 id="qaf-2fa">' . esc_html__( 'التحقق بخطوتين', 'qafesheh' ) . '</h2>';
	echo '<table class="form-table" role="presentation"><tr><th>' . esc_html__( 'الحالة', 'qafesheh' ) . '</th><td>';

	if ( ! $own ) {
		if ( $on ) {
			echo '<p><span class="qaf-2fa-pill is-on">' . esc_html__( 'مفعّل', 'qafesheh' ) . '</span></p>';
			echo '<label><input type="checkbox" name="qaf_2fa_reset" value="1"> ' . esc_html__( 'إيقافه لهذا الشخص (مثلاً أضاع هاتفه). بيقدر يفعّله من جديد من ملفه الشخصي.', 'qafesheh' ) . '</label>';
		} else {
			echo '<p><span class="qaf-2fa-pill">' . esc_html__( 'متوقف', 'qafesheh' ) . '</span> ' . esc_html__( 'صاحب الحساب فقط يقدر يفعّله.', 'qafesheh' ) . '</p>';
		}
		echo '</td></tr></table>';
		return;
	}

	$codes = get_transient( 'qaf_2fa_codes_' . $user->ID );
	if ( $codes ) {
		delete_transient( 'qaf_2fa_codes_' . $user->ID );
		echo '<div class="qaf-2fa-codes"><strong>' . esc_html__( 'رموز الاسترداد — احفظها الآن، ما بتظهر إلا مرة وحدة:', 'qafesheh' ) . '</strong><ol>';
		foreach ( (array) $codes as $c ) {
			echo '<li><code>' . esc_html( $c ) . '</code></li>';
		}
		echo '</ol><p>' . esc_html__( 'كل رمز بيشتغل مرة وحدة إذا أضعت هاتفك. احفظها بمدير كلمات مرور أو اطبعها.', 'qafesheh' ) . '</p></div>';
	}

	if ( $on ) {
		$left = count( (array) get_user_meta( $user->ID, '_qaf_2fa_recovery', true ) );
		echo '<p><span class="qaf-2fa-pill is-on">' . esc_html__( 'مفعّل', 'qafesheh' ) . '</span> ' . esc_html( sprintf( /* translators: %d: count */ _n( 'باقي %d رمز استرداد', 'باقي %d رموز استرداد', $left, 'qafesheh' ), $left ) ) . '</p>';
		echo '<p><label><input type="checkbox" name="qaf_2fa_regen" value="1"> ' . esc_html__( 'إنشاء رموز استرداد جديدة', 'qafesheh' ) . '</label></p>';
		echo '<p><label><input type="checkbox" name="qaf_2fa_off" value="1"> ' . esc_html__( 'إيقاف التحقق بخطوتين', 'qafesheh' ) . '</label></p>';
		echo '<p><label>' . esc_html__( 'الرمز الحالي من التطبيق (مطلوب لأي تغيير):', 'qafesheh' ) . '<br><input type="text" name="qaf_2fa_now" inputmode="numeric" autocomplete="one-time-code" maxlength="7" class="regular-text" style="max-width:160px"></label></p>';
	} else {
		$pending = qaf_2fa_open( get_user_meta( $user->ID, '_qaf_2fa_pending', true ) );
		if ( ! $pending ) {
			$pending = qaf_2fa_b32enc( random_bytes( 20 ) );
			update_user_meta( $user->ID, '_qaf_2fa_pending', qaf_2fa_seal( $pending ) );
		}
		$issuer = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
		$uri    = 'otpauth://totp/' . rawurlencode( $issuer . ':' . $user->user_login ) . '?secret=' . $pending . '&issuer=' . rawurlencode( $issuer ) . '&digits=6&period=30';
		echo '<p><span class="qaf-2fa-pill">' . esc_html__( 'متوقف', 'qafesheh' ) . '</span> ' . esc_html__( 'احمِ حسابك حتى لو عرف أحد كلمة المرور.', 'qafesheh' ) . '</p>';
		echo '<div class="qaf-2fa-setup"><div class="qaf-2fa-qr" data-qaf-qr="' . esc_attr( $uri ) . '"></div><div><ol>';
		echo '<li>' . esc_html__( 'ثبّت تطبيق مصادقة (Google Authenticator أو Microsoft Authenticator أو 1Password…).', 'qafesheh' ) . '</li>';
		echo '<li>' . esc_html__( 'امسح رمز QR بالتطبيق — أو اكتب المفتاح:', 'qafesheh' ) . ' <code class="qaf-2fa-key">' . esc_html( trim( chunk_split( $pending, 4, ' ' ) ) ) . '</code></li>';
		echo '<li>' . esc_html__( 'اكتب الرمز المكوّن من ٦ أرقام الظاهر بالتطبيق، ثم اضغط «تحديث الملف الشخصي».', 'qafesheh' ) . '</li></ol>';
		echo '<input type="text" name="qaf_2fa_confirm" inputmode="numeric" autocomplete="one-time-code" maxlength="7" placeholder="123456" class="regular-text" style="max-width:160px;font-size:18px;letter-spacing:.2em"></div></div>';
	}
	echo '</td></tr></table>';
}
add_action( 'show_user_profile', 'qaf_2fa_profile' );
add_action( 'edit_user_profile', 'qaf_2fa_profile' );

/**
 * Save profile changes (with validation errors shown by WordPress).
 *
 * @param WP_Error $errors Errors.
 * @param bool     $update Update?
 * @param stdClass $user   User data.
 */
function qaf_2fa_profile_save( $errors, $update, $user ) {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- core verified the profile nonce.
	if ( ! $update || ! qaf_2fa_available() || empty( $user->ID ) ) {
		return;
	}
	$uid = (int) $user->ID;
	if ( get_current_user_id() !== $uid ) {
		if ( ! empty( $_POST['qaf_2fa_reset'] ) && current_user_can( 'edit_user', $uid ) && current_user_can( 'manage_options' ) ) {
			delete_user_meta( $uid, '_qaf_2fa_secret' );
			delete_user_meta( $uid, '_qaf_2fa_recovery' );
		}
		return;
	}
	$confirm = isset( $_POST['qaf_2fa_confirm'] ) ? sanitize_text_field( wp_unslash( $_POST['qaf_2fa_confirm'] ) ) : '';
	if ( '' !== $confirm && ! qaf_2fa_enabled( $uid ) ) {
		$pending = qaf_2fa_open( get_user_meta( $uid, '_qaf_2fa_pending', true ) );
		if ( $pending && qaf_2fa_verify_code( $pending, $confirm, 0 ) ) {
			update_user_meta( $uid, '_qaf_2fa_secret', qaf_2fa_seal( $pending ) );
			update_user_meta( $uid, '_qaf_2fa_last', (int) floor( time() / 30 ) );
			delete_user_meta( $uid, '_qaf_2fa_pending' );
			set_transient( 'qaf_2fa_codes_' . $uid, qaf_2fa_new_recovery( $uid ), 10 * MINUTE_IN_SECONDS );
		} else {
			$errors->add( 'qaf_2fa', __( '<strong>التحقق بخطوتين:</strong> الرمز غير مطابق. امسح رمز QR من جديد واكتب أحدث رمز.', 'qafesheh' ) );
		}
	}
	$off   = ! empty( $_POST['qaf_2fa_off'] );
	$regen = ! empty( $_POST['qaf_2fa_regen'] );
	if ( ( $off || $regen ) && qaf_2fa_enabled( $uid ) ) {
		$now = isset( $_POST['qaf_2fa_now'] ) ? sanitize_text_field( wp_unslash( $_POST['qaf_2fa_now'] ) ) : '';
		if ( ! qaf_2fa_verify_code( qaf_2fa_secret( $uid ), $now, $uid ) ) {
			$errors->add( 'qaf_2fa', __( '<strong>التحقق بخطوتين:</strong> اكتب الرمز الحالي من التطبيق لإجراء أي تغيير.', 'qafesheh' ) );
			return;
		}
		if ( $off ) {
			delete_user_meta( $uid, '_qaf_2fa_secret' );
			delete_user_meta( $uid, '_qaf_2fa_recovery' );
		} else {
			set_transient( 'qaf_2fa_codes_' . $uid, qaf_2fa_new_recovery( $uid ), 10 * MINUTE_IN_SECONDS );
		}
	}
	// phpcs:enable
}
add_action( 'user_profile_update_errors', 'qaf_2fa_profile_save', 10, 3 );

/* Assets for the profile screen. */
add_action(
	'admin_enqueue_scripts',
	static function ( $hook ) {
		if ( ! in_array( $hook, array( 'profile.php', 'user-edit.php' ), true ) || ! qaf_2fa_available() ) {
			return;
		}
		wp_enqueue_script( 'qaf-qrcode', QAF_URI . 'assets/js/vendor/qrcode.min.js', array(), QAF_VERSION, true );
		wp_add_inline_script(
			'qaf-qrcode',
			'document.querySelectorAll("[data-qaf-qr]").forEach(function(el){try{var q=qrcode(0,"M");q.addData(el.getAttribute("data-qaf-qr"));q.make();el.innerHTML=q.createSvgTag({cellSize:5,margin:2,scalable:true});}catch(e){}});'
		);
		wp_add_inline_style(
			'wp-admin',
			'.qaf-2fa-pill{display:inline-block;padding:3px 12px;border-radius:99px;background:#E9E3D3;color:#45524F;font-weight:600;font-size:12px;margin-right:8px}.qaf-2fa-pill.is-on{background:#0F0E0C;color:#D9B85C}.qaf-2fa-setup{display:flex;gap:24px;align-items:flex-start;flex-wrap:wrap;margin-top:12px;padding:20px;border:1px solid #E4DCC8;border-radius:18px;background:#FFFDF8;max-width:760px}.qaf-2fa-qr{width:180px;height:180px;flex:none;background:#fff;border-radius:12px;padding:6px;border:1px solid #EFE8D8}.qaf-2fa-qr svg{width:100%;height:100%;display:block}.qaf-2fa-setup ol{margin:0 0 14px 18px}.qaf-2fa-setup li{margin-bottom:8px}.qaf-2fa-key{font-size:13px;letter-spacing:.06em}.qaf-2fa-codes{max-width:620px;padding:18px 22px;margin:0 0 16px;border-radius:16px;background:#FFF8E6;border:1px solid #E8CF94}.qaf-2fa-codes ol{columns:2;margin:12px 0 10px 20px}.qaf-2fa-codes code{font-size:14px;letter-spacing:.06em}'
		);
	}
);

/* ================================================= Encourage administrators */

/**
 * Nudge administrators who have not turned two-step sign-in on.
 */
function qaf_2fa_nudge() {
	{
		if ( ! qaf_2fa_available() || ! qaf_opt( 'twofa_admins', 1 ) || ! current_user_can( 'manage_options' ) || qaf_2fa_enabled( get_current_user_id() ) ) {
			return;
		}
		$screen = get_current_screen();
		if ( ! $screen || ! in_array( $screen->id, array( 'dashboard', 'toplevel_page_qaf-home', 'profile' ), true ) ) {
			return;
		}
		echo '<div class="notice notice-warning qaf-2fa-nudge"><p><strong>' . esc_html__( 'احمِ حسابك:', 'qafesheh' ) . '</strong> ' . esc_html__( 'فعّل التحقق بخطوتين حتى ما تكفي كلمة المرور المسروقة للدخول. بياخذ دقيقة وحدة.', 'qafesheh' ) . ' <a href="' . esc_url( admin_url( 'profile.php#qaf-2fa' ) ) . '">' . esc_html__( 'تفعيله الآن', 'qafesheh' ) . ' →</a></p></div>';
	}
}

add_filter(
	'qaf_security_checks',
	static function ( $rows ) {
		$admins = get_users( array( 'role' => 'administrator', 'fields' => 'ID' ) );
		$with   = array_filter( $admins, 'qaf_2fa_enabled' );
		$rows[] = array(
			'label'  => __( 'التحقق بخطوتين (المدراء)', 'qafesheh' ),
			'state'  => $admins && count( $with ) === count( $admins ) ? 'ok' : 'warn',
			'detail' => sprintf( /* translators: 1: with 2: total */ __( '%1$d من %2$d مدراء محميّين', 'qafesheh' ), count( $with ), count( $admins ) ),
		);
		return $rows;
	},
	6
);
