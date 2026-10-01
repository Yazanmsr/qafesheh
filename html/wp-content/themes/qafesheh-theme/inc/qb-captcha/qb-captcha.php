<?php
/**
 * QB Captcha — portable, theme-agnostic bot protection for every form.
 *
 * Drop the whole `qb-captcha/` folder into any theme, require this file and
 * feed it settings through the `qb_captcha_config` filter. Nothing else.
 *
 * Providers
 *  - shield    : built-in "QB Shield" proof-of-work. No account, no cookies,
 *                no third-party requests. The server signs a challenge, the
 *                browser solves it in a Web Worker, the server checks the
 *                answer and burns it (no replay). Cache-safe (LiteSpeed etc.).
 *  - turnstile : Cloudflare Turnstile (site + secret key).
 *  - recaptcha : Google reCAPTCHA — v2 checkbox keys for "checkbox" mode,
 *                v3 keys for "invisible" mode.
 *  - off
 *
 * Modes: "invisible" (nothing to click) or "checkbox" ("I'm not a robot").
 *
 * Protects: theme forms (call qb_captcha_field() inside the <form> and
 * qb_captcha_verify() in the handler), WordPress login / lost password /
 * register, comments, WooCommerce login / register.
 *
 * JS API: QBCaptcha.ensure( form ) → Promise<token>, QBCaptcha.reset( form ).
 *
 * @package QB_Captcha
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( defined( 'QB_CAPTCHA_VERSION' ) ) {
	return;
}
define( 'QB_CAPTCHA_VERSION', '1.0.0' );
define( 'QB_CAPTCHA_DIR', __DIR__ );

/* ================================================================ Config */

/**
 * Settings (filterable). Themes map their own options onto these keys.
 *
 * @return array
 */
function qb_captcha_config() {
	static $cfg = null;
	if ( null !== $cfg ) {
		return $cfg;
	}
	$dir = wp_normalize_path( QB_CAPTCHA_DIR );
	$tpl = wp_normalize_path( (string) ( realpath( get_template_directory() ) ?: get_template_directory() ) );
	$sty = wp_normalize_path( (string) ( realpath( get_stylesheet_directory() ) ?: get_stylesheet_directory() ) );
	$dir = wp_normalize_path( (string) ( realpath( QB_CAPTCHA_DIR ) ?: $dir ) );
	if ( 0 === strpos( $dir, $sty ) ) {
		$url = get_stylesheet_directory_uri() . substr( $dir, strlen( $sty ) );
	} elseif ( 0 === strpos( $dir, $tpl ) ) {
		$url = get_template_directory_uri() . substr( $dir, strlen( $tpl ) );
	} else {
		$url = plugins_url( '', QB_CAPTCHA_DIR . '/qb-captcha.php' );
	}
	$cfg = apply_filters(
		'qb_captcha_config',
		array(
			'provider'   => 'shield',    // shield | turnstile | recaptcha | off.
			'mode'       => 'invisible', // invisible | checkbox.
			'site_key'   => '',
			'secret_key' => '',
			'strength'   => 'balanced',  // light | balanced | strong.
			'threshold'  => 0.5,         // reCAPTCHA v3 score.
			'login'      => true,        // Login, lost password, register.
			'comments'   => true,
			'brand'      => 'QB Shield',
			'fail_open'  => true,        // Only when Google/Cloudflare can't be reached.
			'url'        => untrailingslashit( $url ),
		)
	);
	$cfg['provider'] = in_array( $cfg['provider'], array( 'shield', 'turnstile', 'recaptcha', 'off' ), true ) ? $cfg['provider'] : 'shield';
	$cfg['mode']     = 'checkbox' === $cfg['mode'] ? 'checkbox' : 'invisible';
	// A third-party provider without keys quietly falls back to the built-in shield.
	if ( in_array( $cfg['provider'], array( 'turnstile', 'recaptcha' ), true ) && ( ! $cfg['site_key'] || ! $cfg['secret_key'] ) ) {
		$cfg['provider'] = 'shield';
	}
	return $cfg;
}

/**
 * Is protection on?
 *
 * @return bool
 */
function qb_captcha_on() {
	return 'off' !== qb_captcha_config()['provider'];
}

/**
 * Proof-of-work difficulty (leading zero bits). Busy IPs get harder puzzles.
 *
 * @return int
 */
function qb_captcha_bits() {
	$map  = array( 'light' => 15, 'balanced' => 17, 'strong' => 19 );
	$bits = $map[ qb_captcha_config()['strength'] ] ?? 17;
	$hits = (int) get_transient( 'qbc_rate_' . qb_captcha_ip_key() );
	if ( $hits > 40 ) {
		$bits += 4;
	} elseif ( $hits > 15 ) {
		$bits += 2;
	}
	return min( 23, (int) apply_filters( 'qb_captcha_bits', $bits, $hits ) );
}

/**
 * Hashed visitor key (never stores the raw IP).
 *
 * @return string
 */
function qb_captcha_ip_key() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '0';
	return substr( hash_hmac( 'sha256', $ip, wp_salt( 'nonce' ) ), 0, 20 );
}

/**
 * Signing secret.
 *
 * @return string
 */
function qb_captcha_secret() {
	return hash( 'sha256', wp_salt( 'auth' ) . '|qb-captcha|' . QB_CAPTCHA_VERSION );
}

/* ================================================================ Challenge endpoint (QB Shield) */

/**
 * Issue a signed challenge. Public, uncached, cheap.
 */
function qb_captcha_ajax_challenge() {
	nocache_headers();
	header( 'X-Robots-Tag: noindex' );
	$key  = 'qbc_rate_' . qb_captcha_ip_key();
	$hits = (int) get_transient( $key );
	set_transient( $key, $hits + 1, 10 * MINUTE_IN_SECONDS );
	if ( $hits > 120 ) {
		wp_send_json_error( array( 'message' => 'slow down' ), 429 );
	}
	$ctx = isset( $_GET['ctx'] ) ? sanitize_key( wp_unslash( $_GET['ctx'] ) ) : 'form'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- public, read-only.
	$c   = implode( '.', array( 'q1', time(), qb_captcha_bits(), bin2hex( random_bytes( 12 ) ), substr( $ctx, 0, 20 ) ) );
	wp_send_json_success(
		array(
			'c' => $c,
			's' => hash_hmac( 'sha256', $c, qb_captcha_secret() ),
		)
	);
}
add_action( 'wp_ajax_qb_captcha_challenge', 'qb_captcha_ajax_challenge' );
add_action( 'wp_ajax_nopriv_qb_captcha_challenge', 'qb_captcha_ajax_challenge' );

/**
 * Count leading zero bits of a binary string.
 *
 * @param string $bin Raw bytes.
 * @return int
 */
function qb_captcha_zero_bits( $bin ) {
	$bits = 0;
	$len  = strlen( $bin );
	for ( $i = 0; $i < $len; $i++ ) {
		$byte = ord( $bin[ $i ] );
		if ( 0 === $byte ) {
			$bits += 8;
			continue;
		}
		for ( $m = 0x80; $m && ! ( $byte & $m ); $m >>= 1 ) {
			++$bits;
		}
		break;
	}
	return $bits;
}

/* ================================================================ Verification */

/**
 * Verify a submitted token.
 *
 * @param string      $ctx   Context (contact, login, comment…).
 * @param string|null $token Token (defaults to $_POST['qb_captcha']).
 * @return true|WP_Error
 */
function qb_captcha_verify( $ctx = 'form', $token = null ) {
	if ( ! qb_captcha_on() || ! apply_filters( 'qb_captcha_required', true, $ctx ) ) {
		return true;
	}
	if ( null === $token ) {
		$token = isset( $_POST['qb_captcha'] ) ? sanitize_text_field( wp_unslash( $_POST['qb_captcha'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- this IS the check.
	}
	$token = (string) $token;
	$cfg   = qb_captcha_config();
	$fail  = new WP_Error( 'qb_captcha', __( 'يرجى تأكيد أنك لست روبوتاً والمحاولة مرة أخرى.', 'qb-captcha' ) );
	if ( '' === $token || strlen( $token ) > 4096 ) {
		return $fail;
	}
	$parts = explode( '|', $token );
	$kind  = array_shift( $parts );

	if ( 'shield' === $cfg['provider'] ) {
		if ( 'qs' !== $kind || 3 !== count( $parts ) ) {
			return $fail;
		}
		list( $c, $sig, $n ) = $parts;
		$bits = explode( '.', $c );
		if ( 5 !== count( $bits ) || 'q1' !== $bits[0] || ! ctype_digit( (string) $n ) || strlen( $n ) > 12 ) {
			return $fail;
		}
		if ( ! hash_equals( hash_hmac( 'sha256', $c, qb_captcha_secret() ), $sig ) ) {
			return $fail;
		}
		$age = time() - (int) $bits[1];
		if ( $age < 0 || $age > 30 * MINUTE_IN_SECONDS ) {
			return new WP_Error( 'qb_captcha', __( 'الصفحة مفتوحة من فترة طويلة — حاول مرة ثانية من فضلك.', 'qb-captcha' ) );
		}
		if ( qb_captcha_zero_bits( hash( 'sha256', $c . ':' . $n, true ) ) < (int) $bits[2] ) {
			return $fail;
		}
		$used = 'qbc_u_' . substr( hash( 'sha256', $c ), 0, 24 );
		if ( get_transient( $used ) ) {
			return $fail; // Replay.
		}
		set_transient( $used, 1, 31 * MINUTE_IN_SECONDS );
		return true;
	}

	// Cloudflare Turnstile / Google reCAPTCHA.
	$want = 'turnstile' === $cfg['provider'] ? 'ts' : 'rc';
	if ( $want !== $kind || 1 !== count( $parts ) || '' === $parts[0] ) {
		return $fail;
	}
	$url = 'turnstile' === $cfg['provider'] ? 'https://challenges.cloudflare.com/turnstile/v0/siteverify' : 'https://www.google.com/recaptcha/api/siteverify';
	$res = wp_remote_post(
		$url,
		array(
			'timeout' => 8,
			'body'    => array(
				'secret'   => $cfg['secret_key'],
				'response' => $parts[0],
				'remoteip' => isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '',
			),
		)
	);
	if ( is_wp_error( $res ) || 200 !== (int) wp_remote_retrieve_response_code( $res ) ) {
		// The provider is unreachable — don't lose a real guest's message over it.
		update_option( 'qb_captcha_last_error', array( 't' => time(), 'e' => is_wp_error( $res ) ? $res->get_error_message() : wp_remote_retrieve_response_code( $res ) ), false );
		return $cfg['fail_open'] ? true : $fail;
	}
	$body = json_decode( wp_remote_retrieve_body( $res ), true );
	if ( empty( $body['success'] ) ) {
		return $fail;
	}
	if ( 'recaptcha' === $cfg['provider'] && 'invisible' === $cfg['mode'] && isset( $body['score'] ) && (float) $body['score'] < (float) $cfg['threshold'] ) {
		return $fail;
	}
	return true;
}

/* ================================================================ Output */

/**
 * Register the assets (once).
 */
function qb_captcha_register_assets() {
	if ( wp_script_is( 'qb-captcha', 'registered' ) ) {
		return;
	}
	$cfg = qb_captcha_config();
	wp_register_style( 'qb-captcha', $cfg['url'] . '/qb-captcha.css', array(), QB_CAPTCHA_VERSION );
	wp_register_script( 'qb-captcha', $cfg['url'] . '/qb-captcha.js', array(), QB_CAPTCHA_VERSION, array( 'in_footer' => true, 'strategy' => 'defer' ) );
	wp_add_inline_script(
		'qb-captcha',
		'window.QBC_CFG=' . wp_json_encode(
			array(
				'ajax'     => admin_url( 'admin-ajax.php' ),
				'provider' => $cfg['provider'],
				'mode'     => $cfg['mode'],
				'key'      => in_array( $cfg['provider'], array( 'turnstile', 'recaptcha' ), true ) ? $cfg['site_key'] : '',
				'i18n'     => array(
					'verifying' => __( 'جارٍ التحقق…', 'qb-captcha' ),
					'verified'  => __( 'تم التحقق', 'qb-captcha' ),
					'robot'     => __( 'لست روبوتاً', 'qb-captcha' ),
					'tick'      => __( 'اضغط على «لست روبوتاً» أولاً.', 'qb-captcha' ),
					'failed'    => __( 'ما قدرنا نتحقق — حاول مرة ثانية.', 'qb-captcha' ),
					'human'     => __( 'تم التحقق', 'qb-captcha' ),
				),
			)
		) . ';',
		'before'
	);
}

/**
 * The widget. Call inside any <form>.
 *
 * @param string $ctx  Context name.
 * @param array  $args { echo, class }.
 * @return string
 */
function qb_captcha_field( $ctx = 'form', $args = array() ) {
	if ( ! qb_captcha_on() ) {
		return '';
	}
	$args = wp_parse_args( $args, array( 'echo' => true, 'class' => '' ) );
	$cfg  = qb_captcha_config();
	qb_captcha_register_assets();
	wp_enqueue_style( 'qb-captcha' );
	wp_enqueue_script( 'qb-captcha' );

	$shield = '<svg class="qbc-shield" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path d="M12 2.5 4.5 5.3v6.1c0 4.7 3.2 8.6 7.5 10.1 4.3-1.5 7.5-5.4 7.5-10.1V5.3L12 2.5Z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path class="qbc-shield__tick" d="m8.6 12.2 2.4 2.4 4.6-4.9" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>';
	$cls    = 'qb-captcha qb-captcha--' . $cfg['mode'] . ' qb-captcha--' . $cfg['provider'] . ( $args['class'] ? ' ' . $args['class'] : '' );
	$html   = '<div class="' . esc_attr( $cls ) . '" data-qb-captcha data-ctx="' . esc_attr( sanitize_key( $ctx ) ) . '">';
	$html  .= '<input type="hidden" name="qb_captcha" value="">';

	if ( 'shield' === $cfg['provider'] && 'checkbox' === $cfg['mode'] ) {
		$html .= '<button type="button" class="qbc-card" role="checkbox" aria-checked="false" data-qbc-box>'
			. '<span class="qbc-box" aria-hidden="true"><svg viewBox="0 0 36 36"><circle class="qbc-ring" cx="18" cy="18" r="16"/><path class="qbc-tick" d="m11 18.5 4.8 4.8L25.5 13"/></svg></span>'
			. '<span class="qbc-copy"><span class="qbc-label" data-qbc-label>' . esc_html__( 'لست روبوتاً', 'qb-captcha' ) . '</span><span class="qbc-sub">' . esc_html__( 'تحقق خاص · بدون ألغاز أو تتبّع', 'qb-captcha' ) . '</span></span>'
			. '<span class="qbc-brand">' . $shield . '<span>' . esc_html( $cfg['brand'] ) . '</span></span>'
			. '</button>';
	} elseif ( 'shield' === $cfg['provider'] ) {
		$html .= '<p class="qbc-quiet">' . $shield . '<span data-qbc-label>' . esc_html(
			sprintf( /* translators: %s: brand */ __( 'محمي بـ %s · خاص وبدون ألغاز', 'qb-captcha' ), $cfg['brand'] )
		) . '</span></p>';
	} else {
		$html .= '<div class="qbc-slot" data-qbc-slot></div>';
		if ( 'recaptcha' === $cfg['provider'] && 'invisible' === $cfg['mode'] ) {
			$html .= '<p class="qbc-quiet qbc-legal">' . $shield . '<span>' . wp_kses(
				__( 'محمي بـ reCAPTCHA — تنطبق <a href="https://policies.google.com/privacy" target="_blank" rel="noopener">سياسة خصوصية</a> و<a href="https://policies.google.com/terms" target="_blank" rel="noopener">شروط</a> Google.', 'qb-captcha' ),
				array( 'a' => array( 'href' => true, 'target' => true, 'rel' => true ) )
			) . '</span></p>';
		}
	}
	$html .= '<noscript><p class="qbc-noscript">' . esc_html__( 'فعّل JavaScript بالمتصفح لإرسال النموذج.', 'qb-captcha' ) . '</p></noscript>';
	$html .= '</div>';

	if ( $args['echo'] ) {
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built above.
	}
	return $html;
}

/* ================================================================ WordPress core forms */

/**
 * Login-type forms enabled?
 *
 * @return bool
 */
function qb_captcha_login_on() {
	return qb_captcha_on() && ! empty( qb_captcha_config()['login'] ) && apply_filters( 'qb_captcha_login', true );
}

add_action( 'login_form', static function () { qb_captcha_login_on() && qb_captcha_field( 'login', array( 'class' => 'qb-captcha--wp' ) ); } );
add_action( 'lostpassword_form', static function () { qb_captcha_login_on() && qb_captcha_field( 'lostpassword', array( 'class' => 'qb-captcha--wp' ) ); } );
add_action( 'register_form', static function () { qb_captcha_login_on() && qb_captcha_field( 'register', array( 'class' => 'qb-captcha--wp' ) ); } );
add_action( 'woocommerce_login_form', static function () { qb_captcha_login_on() && qb_captcha_field( 'login' ); } );
add_action( 'woocommerce_register_form', static function () { qb_captcha_login_on() && qb_captcha_field( 'register' ); } );
add_action( 'woocommerce_lostpassword_form', static function () { qb_captcha_login_on() && qb_captcha_field( 'lostpassword' ); } );

/**
 * Login check — runs only for real form logins (not cookies, REST, XML-RPC, app passwords).
 *
 * @param WP_User|WP_Error|null $user User.
 * @return WP_User|WP_Error|null
 */
function qb_captcha_authenticate( $user ) {
	// phpcs:disable WordPress.Security.NonceVerification.Missing
	$form = ( did_action( 'login_init' ) && isset( $_POST['log'] ) ) || isset( $_POST['woocommerce-login-nonce'] );
	// phpcs:enable
	if ( ! $form || ! qb_captcha_login_on() || ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return $user;
	}
	$ok = qb_captcha_verify( 'login' );
	return is_wp_error( $ok ) ? $ok : $user;
}
add_filter( 'authenticate', 'qb_captcha_authenticate', 30 );

add_action(
	'lostpassword_post',
	static function ( $errors ) {
		if ( ! qb_captcha_login_on() || ! ( $errors instanceof WP_Error ) || ! isset( $_POST['user_login'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			return;
		}
		$ok = qb_captcha_verify( 'lostpassword' );
		if ( is_wp_error( $ok ) ) {
			$errors->add( 'qb_captcha', $ok->get_error_message() );
		}
	}
);

$qb_captcha_reg = static function ( $errors ) {
	if ( qb_captcha_login_on() && $errors instanceof WP_Error ) {
		$ok = qb_captcha_verify( 'register' );
		if ( is_wp_error( $ok ) ) {
			$errors->add( 'qb_captcha', $ok->get_error_message() );
		}
	}
	return $errors;
};
add_filter( 'registration_errors', $qb_captcha_reg, 20 );
add_filter( 'woocommerce_process_registration_errors', $qb_captcha_reg, 20 );

/* Login screen assets (the widget enqueues itself, this just makes sure styles print in <head>). */
add_action(
	'login_enqueue_scripts',
	static function () {
		if ( qb_captcha_login_on() ) {
			qb_captcha_register_assets();
			wp_enqueue_style( 'qb-captcha' );
		}
	}
);

/* ================================================================ Comments */

/**
 * Comments enabled?
 *
 * @return bool
 */
function qb_captcha_comments_on() {
	return qb_captcha_on() && ! empty( qb_captcha_config()['comments'] ) && ! is_user_logged_in();
}

add_filter(
	'comment_form_submit_field',
	static function ( $field ) {
		return qb_captcha_comments_on() ? qb_captcha_field( 'comment', array( 'echo' => false ) ) . $field : $field;
	}
);

add_filter(
	'preprocess_comment',
	static function ( $data ) {
		if ( ! qb_captcha_comments_on() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) ) {
			return $data;
		}
		if ( in_array( $data['comment_type'] ?? '', array( 'pingback', 'trackback' ), true ) ) {
			return $data;
		}
		$ok = qb_captcha_verify( 'comment' );
		if ( is_wp_error( $ok ) ) {
			wp_die( esc_html( $ok->get_error_message() ), esc_html__( 'لم يتم إرسال التعليق', 'qb-captcha' ), array( 'response' => 403, 'back_link' => true ) );
		}
		return $data;
	},
	1
);

/* ================================================================ Status (for dashboards) */

/**
 * One-line status for admin screens.
 *
 * @return array { state: ok|warn, label }
 */
function qb_captcha_status() {
	$cfg   = qb_captcha_config();
	$names = array( 'shield' => $cfg['brand'], 'turnstile' => 'Cloudflare Turnstile', 'recaptcha' => 'Google reCAPTCHA' );
	if ( ! qb_captcha_on() ) {
		return array( 'state' => 'warn', 'label' => __( 'متوقف — النماذج تعتمد على الحماية الخفية فقط', 'qb-captcha' ) );
	}
	$mode = 'checkbox' === $cfg['mode'] ? __( '«لست روبوتاً»', 'qb-captcha' ) : __( 'غير مرئي', 'qb-captcha' );
	$err  = get_option( 'qb_captcha_last_error' );
	$note = ( $err && ( $err['t'] ?? 0 ) > time() - DAY_IN_SECONDS ) ? ' · ' . __( 'تعذّر الوصول لمزوّد الخدمة مؤخراً', 'qb-captcha' ) : '';
	return array(
		'state' => $note ? 'warn' : 'ok',
		'label' => $names[ $cfg['provider'] ] . ' · ' . $mode . $note,
	);
}
