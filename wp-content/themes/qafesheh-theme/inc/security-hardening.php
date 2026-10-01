<?php
/**
 * Security hardening: firewall, server rules, alerts, fingerprints, headers.
 *
 * Everything is switchable in Theme Settings → Security and reported in the
 * Launch centre. Emergency off-switch: define( 'QAF_NO_HARDENING', true );
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

/**
 * Master switch.
 *
 * @return bool
 */
function qaf_hardening_on() {
	return ! ( defined( 'QAF_NO_HARDENING' ) && QAF_NO_HARDENING );
}

/* ================================================= Firewall (WAF-lite) */

/**
 * Rules that only an attack would ever send. Conservative on purpose:
 * we look at the address and user agent, never at form bodies.
 *
 * @return array name => regex
 */
function qaf_waf_rules() {
	return apply_filters(
		'qaf_waf_rules',
		array(
			'traversal' => '#(\.\./|\.\.\\\\|%2e%2e%2f|%2e%2e/|\.\.%2f){2,}#i',
			'sys-files' => '#(/etc/(passwd|shadow|hosts)|proc/self/environ|boot\.ini|win\.ini)#i',
			'sqli'      => '#(union(\s|\+|/\*.*?\*/)+(all(\s|\+)+)?select\b|\b(sleep|benchmark|pg_sleep)\s*\(\s*\d|information_schema\.|\bwaitfor\s+delay\b|;\s*(drop|truncate)\s+table)#i',
			'xss'       => '#(<|%3c)\s*(script|iframe|svg[^a-z]|img[^a-z][^>]*on\w+\s*=)|javascript\s*:|\bon(error|load|mouseover)\s*=#i',
			'php-exec'  => '#\b(base64_decode|eval|gzinflate|str_rot13|assert|shell_exec|passthru|system|phpinfo)\s*\(#i',
			'secrets'   => '#/(\.env|\.git/|\.svn/|\.htpasswd|wp-config\.php\.(bak|old|save|orig|txt|swp)|\.DS_Store|composer\.(json|lock)|debug\.log)#i',
			'scanners'  => '#^(sqlmap|nikto|wpscan|acunetix|nessus|masscan|zgrab|nuclei|dirbuster|gobuster|fimap|w3af|havij|netsparker|jorgee)#i',
		)
	);
}

/**
 * Inspect the request as early as a theme can.
 */
function qaf_waf() {
	if ( ! qaf_hardening_on() || ! qaf_opt( 'waf', 1 ) || ( defined( 'WP_CLI' ) && WP_CLI ) || ( defined( 'DOING_CRON' ) && DOING_CRON ) ) {
		return;
	}
	$uri = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_unslash( $_SERVER['REQUEST_URI'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- inspected, never output.
	$ua  = isset( $_SERVER['HTTP_USER_AGENT'] ) ? (string) wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$dec = rawurldecode( rawurldecode( $uri ) );
	foreach ( qaf_waf_rules() as $name => $re ) {
		$hay = 'scanners' === $name ? $ua : $uri . "\n" . $dec;
		if ( '' !== $hay && preg_match( $re, $hay ) ) {
			qaf_waf_block( $name );
		}
	}
}
add_action( 'after_setup_theme', 'qaf_waf', 0 );

/**
 * Stop the request (and remember it for the Launch centre).
 *
 * @param string $rule Rule name.
 */
function qaf_waf_block( $rule ) {
	$log   = (array) get_option( 'qaf_waf_log', array() );
	$log[] = array(
		't' => time(),
		'r' => $rule,
		'i' => substr( hash_hmac( 'sha256', qaf_client_ip(), wp_salt( 'nonce' ) ), 0, 10 ),
	);
	update_option( 'qaf_waf_log', array_slice( $log, -100 ), false );
	status_header( 403 );
	nocache_headers();
	header( 'Content-Type: text/html; charset=utf-8' );
	echo '<!doctype html><meta charset="utf-8"><meta name="robots" content="noindex"><title>403</title><body style="margin:0;display:grid;place-items:center;min-height:100vh;background:#F7F1E1;font-family:system-ui,sans-serif;color:#1B2624;text-align:center"><div><p style="font:italic 28px Georgia,serif;color:#003E3B;margin:0 0 8px">Feta &amp; Olive</p><p style="margin:0;color:#45524F">This request was blocked for security reasons.</p><p><a href="/" style="color:#003E3B">Back to the homepage</a></p></div></body>';
	exit;
}

/**
 * Blocked requests in the last 7 days.
 *
 * @return int
 */
function qaf_waf_count() {
	return count( array_filter( (array) get_option( 'qaf_waf_log', array() ), static fn( $r ) => ( $r['t'] ?? 0 ) > time() - WEEK_IN_SECONDS ) );
}

/* ================================================= Server rules (.htaccess) */

/**
 * Root rules.
 *
 * @return string[]
 */
function qaf_htaccess_root_rules() {
	$files = 'wp-config\.php|wp-config-sample\.php|readme\.html|license\.txt|\.htaccess|\.htpasswd|\.user\.ini|php\.ini|error_log|debug\.log|\.env';
	if ( qaf_opt( 'disable_xmlrpc', 1 ) ) {
		$files .= '|xmlrpc\.php';
	}
	return array(
		'# Built by the مجموعة قفيشة theme. Switch off in Theme Settings → Security.',
		'<FilesMatch "^(' . $files . ')$">',
		'	<IfModule mod_authz_core.c>',
		'		Require all denied',
		'	</IfModule>',
		'	<IfModule !mod_authz_core.c>',
		'		Order allow,deny',
		'		Deny from all',
		'	</IfModule>',
		'</FilesMatch>',
		'<IfModule mod_rewrite.c>',
		'	RewriteEngine On',
		'	RewriteRule ^wp-admin/includes/ - [F,L]',
		'	RewriteRule !^wp-includes/ - [S=3]',
		'	RewriteRule ^wp-includes/[^/]+\.php$ - [F,L]',
		'	RewriteRule ^wp-includes/js/tinymce/langs/.+\.php - [F,L]',
		'	RewriteRule ^wp-includes/theme-compat/ - [F,L]',
		'</IfModule>',
	);
}

/**
 * Uploads rules: photos only, never scripts.
 *
 * @return string[]
 */
function qaf_htaccess_upload_rules() {
	return array(
		'# Built by the مجموعة قفيشة theme: no scripts can run from uploads.',
		'<FilesMatch "\.(php|php[0-9]|phtml|phar|pht|phps|pl|py|cgi|sh|asp|aspx|jsp)$">',
		'	<IfModule mod_authz_core.c>',
		'		Require all denied',
		'	</IfModule>',
		'	<IfModule !mod_authz_core.c>',
		'		Order allow,deny',
		'		Deny from all',
		'	</IfModule>',
		'</FilesMatch>',
	);
}

/**
 * Can we write .htaccess here (Apache / LiteSpeed)?
 *
 * @return bool
 */
function qaf_htaccess_supported() {
	global $is_apache;
	$soft = isset( $_SERVER['SERVER_SOFTWARE'] ) ? strtolower( sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) ) : '';
	return $is_apache || false !== strpos( $soft, 'litespeed' ) || false !== strpos( $soft, 'apache' );
}

/**
 * Write (or remove) the rules, then make sure the site still loads.
 * If the host rejects anything, the rules are rolled back immediately.
 *
 * @return string ok | off | unsupported | rolled-back | not-writable
 */
function qaf_htaccess_sync() {
	if ( ! function_exists( 'insert_with_markers' ) ) {
		require_once ABSPATH . 'wp-admin/includes/misc.php';
	}
	$want = qaf_hardening_on() && qaf_opt( 'harden_htaccess', 1 );
	if ( ! qaf_htaccess_supported() ) {
		update_option( 'qaf_htaccess_state', 'unsupported', false );
		return 'unsupported';
	}
	$root    = ABSPATH . '.htaccess';
	$up      = wp_upload_dir( null, false );
	$uploads = trailingslashit( $up['basedir'] ) . '.htaccess';
	$root_ok = ( file_exists( $root ) && wp_is_writable( $root ) ) || ( ! file_exists( $root ) && wp_is_writable( ABSPATH ) );
	$up_ok   = ( file_exists( $uploads ) && wp_is_writable( $uploads ) ) || ( ! file_exists( $uploads ) && wp_is_writable( $up['basedir'] ) );
	if ( ! $root_ok && ! $up_ok ) {
		update_option( 'qaf_htaccess_state', 'not-writable', false );
		return 'not-writable';
	}
	if ( $root_ok ) {
		insert_with_markers( $root, 'مجموعة قفيشة Security', $want ? qaf_htaccess_root_rules() : array() );
	}
	if ( $up_ok ) {
		insert_with_markers( $uploads, 'مجموعة قفيشة Security', $want ? qaf_htaccess_upload_rules() : array() );
	}
	if ( ! $want ) {
		update_option( 'qaf_htaccess_state', 'off', false );
		return 'off';
	}
	// Safety net: a server that doesn't allow these directives answers 500 — undo at once.
	$check = wp_remote_get( add_query_arg( 'qaf-htcheck', wp_rand(), home_url( '/' ) ), array( 'timeout' => 8, 'sslverify' => false, 'redirection' => 3 ) );
	if ( ! is_wp_error( $check ) && 500 <= (int) wp_remote_retrieve_response_code( $check ) ) {
		if ( $root_ok ) {
			insert_with_markers( $root, 'مجموعة قفيشة Security', array() );
		}
		if ( $up_ok ) {
			insert_with_markers( $uploads, 'مجموعة قفيشة Security', array() );
		}
		update_option( 'qaf_htaccess_state', 'rolled-back', false );
		return 'rolled-back';
	}
	update_option( 'qaf_htaccess_state', 'ok', false );
	return 'ok';
}
add_action( 'after_switch_theme', 'qaf_htaccess_sync' );
add_action(
	'update_option_qaf_options',
	static function ( $old, $new ) {
		$keys = array( 'harden_htaccess', 'disable_xmlrpc' );
		foreach ( $keys as $k ) {
			if ( ( $old[ $k ] ?? null ) !== ( $new[ $k ] ?? null ) ) {
				// Options are cached per request; re-read after the save completes.
				add_action( 'shutdown', 'qaf_htaccess_sync' );
				return;
			}
		}
	},
	10,
	2
);
/* First visit to the dashboard after an update: write the rules once. */
add_action(
	'admin_init',
	static function () {
		if ( current_user_can( 'manage_options' ) && get_option( 'qaf_htaccess_ver' ) !== QAF_VERSION ) {
			update_option( 'qaf_htaccess_ver', QAF_VERSION, false );
			qaf_htaccess_sync();
		}
	}
);

/* ================================================= Alerts */

/**
 * Send a security alert to the owner.
 *
 * @param string $subject Subject.
 * @param string $heading Heading.
 * @param array  $rows    Details.
 * @param string $advice  Advice line.
 */
function qaf_security_alert( $subject, $heading, $rows, $advice ) {
	if ( ! qaf_opt( 'admin_alerts', 1 ) ) {
		return;
	}
	$to  = array_unique( array_filter( array( get_option( 'admin_email' ), sanitize_email( (string) qaf_opt( 'c_email', '' ) ) ) ) );
	$who = wp_get_current_user();
	$rows[ __( 'بواسطة', 'qafesheh' ) ] = $who && $who->exists() ? $who->display_name . ' (' . $who->user_login . ')' : __( 'غير مسجّل / تلقائي', 'qafesheh' );
	$rows[ __( 'الوقت', 'qafesheh' ) ]    = wp_date( get_option( 'date_format' ) . ' · ' . get_option( 'time_format' ) );
	$rows[ __( 'عنوان IP', 'qafesheh' ) ] = qaf_client_ip();
	$html = qaf_email_p( __( 'هذا تنبيه أمني تلقائي من موقعكم.', 'qafesheh' ) )
		. qaf_email_rows( $rows )
		. qaf_email_p( $advice )
		. qaf_email_button( __( 'مراجعة الفريق', 'qafesheh' ), admin_url( 'users.php' ) );
	$GLOBALS['qaf_email_raw'] = true;
	foreach ( $to as $email ) {
		wp_mail( $email, '🔒 ' . $subject, qaf_email_document( $heading, $html, __( 'تنبيه أمني', 'qafesheh' ), array( 'preheader' => $subject ) ), array( 'Content-Type: text/html; charset=UTF-8' ) );
	}
	unset( $GLOBALS['qaf_email_raw'] );
}

/**
 * New administrator (created, or promoted).
 *
 * @param int    $user_id   User.
 * @param string $role      New role.
 * @param array  $old_roles Old roles.
 */
function qaf_alert_role( $user_id, $role, $old_roles = array() ) {
	if ( 'administrator' !== $role || in_array( 'administrator', (array) $old_roles, true ) ) {
		return;
	}
	static $sent = array();
	$u = get_userdata( $user_id );
	if ( ! $u || isset( $sent[ $user_id ] ) ) {
		return;
	}
	$sent[ $user_id ] = true;
	qaf_security_alert(
		sprintf( /* translators: %s: user */ __( 'مدير جديد: %s', 'qafesheh' ), $u->user_login ),
		__( 'حساب صار مديراً للموقع', 'qafesheh' ),
		array( __( 'الحساب', 'qafesheh' ) => $u->display_name . ' (' . $u->user_login . ')', __( 'البريد', 'qafesheh' ) => $u->user_email ),
		__( 'المدير يقدر يغيّر كل شي بالموقع. إذا ما كنت متوقع هذا، احذف الحساب وغيّر كلمة المرور فوراً.', 'qafesheh' )
	);
}
add_action( 'set_user_role', 'qaf_alert_role', 10, 3 );
add_action(
	'add_user_role',
	static function ( $user_id, $role ) {
		qaf_alert_role( $user_id, $role );
	},
	10,
	2
);
add_action(
	'user_register',
	static function ( $user_id ) {
		$u = get_userdata( $user_id );
		if ( $u && in_array( 'administrator', (array) $u->roles, true ) ) {
			qaf_alert_role( $user_id, 'administrator' );
		}
	}
);

/**
 * Administrator signed in from a new device.
 *
 * @param string  $login User login.
 * @param WP_User $user  User.
 */
function qaf_alert_new_device( $login, $user ) {
	if ( ! $user instanceof WP_User || ! user_can( $user, 'manage_options' ) ) {
		return;
	}
	$ua    = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
	$ip    = qaf_client_ip();
	$net   = preg_replace( '/\.\d+$/', '.0', $ip ); // Same network counts as the same place.
	$print = substr( hash_hmac( 'sha256', $ua . '|' . $net, wp_salt( 'auth' ) ), 0, 16 );
	$known = (array) get_user_meta( $user->ID, '_qaf_devices', true );
	if ( in_array( $print, $known, true ) ) {
		return;
	}
	$first = ! $known;
	$known[] = $print;
	update_user_meta( $user->ID, '_qaf_devices', array_slice( $known, -12 ) );
	if ( $first ) {
		return; // Don't alarm on the very first sign-in we ever see.
	}
	$browser = preg_match( '/(Edg|Chrome|Firefox|Safari|OPR)\/[\d.]+/', $ua, $m ) ? $m[0] : __( 'متصفح غير معروف', 'qafesheh' );
	$os      = preg_match( '/\((Windows[^;)]*|Macintosh[^;)]*|iPhone[^;)]*|iPad[^;)]*|Android[^;)]*|Linux[^;)]*)/', $ua, $o ) ? $o[1] : '';
	qaf_security_alert(
		sprintf( /* translators: %s: user */ __( 'دخول جديد للوحة التحكم: %s', 'qafesheh' ), $user->user_login ),
		__( 'دخول من جهاز جديد', 'qafesheh' ),
		array( __( 'الحساب', 'qafesheh' ) => $user->display_name . ' (' . $user->user_login . ')', __( 'الجهاز', 'qafesheh' ) => trim( $browser . ' · ' . $os, ' ·' ) ),
		__( 'إذا كان هذا أنت، ما في شي تعمله. وإذا لا، غيّر كلمة المرور فوراً وفعّل التحقق بخطوتين من ملفك الشخصي.', 'qafesheh' )
	);
}
add_action( 'wp_login', 'qaf_alert_new_device', 20, 2 );

/* ================================================= Fingerprints + extras */

/**
 * Hide the WordPress version in asset URLs for visitors (keeps cache-busting).
 *
 * @param string $src URL.
 * @return string
 */
function qaf_hide_core_ver( $src ) {
	if ( is_admin() || ! $src || false === strpos( $src, 'ver=' ) ) {
		return $src;
	}
	$wp = get_bloginfo( 'version' );
	if ( false !== strpos( $src, 'ver=' . $wp ) ) {
		$src = str_replace( 'ver=' . $wp, 'ver=' . substr( md5( $wp . wp_salt( 'nonce' ) ), 0, 8 ), $src );
	}
	return $src;
}
add_filter( 'style_loader_src', 'qaf_hide_core_ver', 20 );
add_filter( 'script_loader_src', 'qaf_hide_core_ver', 20 );

/* Application passwords bypass the login page (and its captcha + two-step). */
add_filter(
	'wp_is_application_passwords_available',
	static function ( $on ) {
		return qaf_opt( 'disable_app_passwords', 1 ) ? false : $on;
	}
);

/* Extra headers: no clickjacking, no mixed content, no plugin objects. */
add_action(
	'send_headers',
	static function () {
		if ( ! qaf_opt( 'security_headers', 1 ) || is_admin() ) {
			return;
		}
		$csp = "frame-ancestors 'self'; base-uri 'self'; object-src 'none'; form-action 'self' https:";
		if ( is_ssl() ) {
			$csp .= '; upgrade-insecure-requests';
		}
		header( 'Content-Security-Policy: ' . apply_filters( 'qaf_csp', $csp ) );
		header( 'Cross-Origin-Opener-Policy: same-origin-allow-popups' );
		header( 'X-Permitted-Cross-Domain-Policies: none' );
	},
	11
);

/* Nothing sensitive on the login screen. */
add_filter( 'login_display_language_dropdown', '__return_false' );

/* Session hygiene: shorter "remember me" for the dashboard (14 days, not 14 + extras). */
add_filter(
	'auth_cookie_expiration',
	static function ( $len, $user_id, $remember ) {
		return $remember ? min( $len, 14 * DAY_IN_SECONDS ) : min( $len, 12 * HOUR_IN_SECONDS );
	},
	10,
	3
);

/* ================================================= Checks for the Launch centre */

add_filter(
	'qaf_security_checks',
	static function ( $rows ) {
		$state  = get_option( 'qaf_htaccess_state', '' );
		$labels = array(
			'ok'           => array( 'ok', __( 'ملفات الإعداد والسجلات ومجلد الرفع محمية', 'qafesheh' ) ),
			'off'          => array( 'warn', __( 'متوقفة', 'qafesheh' ) ),
			'unsupported'  => array( 'ok', __( 'السيرفر ليس Apache/LiteSpeed — جدار الحماية داخل الثيم يحمي الموقع', 'qafesheh' ) ),
			'rolled-back'  => array( 'warn', __( 'الاستضافة رفضت القواعد فانحذفت تلقائياً', 'qafesheh' ) ),
			'not-writable' => array( 'warn', __( 'ملف .htaccess للقراءة فقط — اطلب من الاستضافة السماح بتعديله', 'qafesheh' ) ),
			''             => array( 'warn', __( 'افتح لوحة التحكم مرة لتطبيقها', 'qafesheh' ) ),
		);
		$s      = $labels[ $state ] ?? $labels[''];
		$rows[] = array( 'label' => __( 'قواعد السيرفر (.htaccess)', 'qafesheh' ), 'state' => $s[0], 'detail' => $s[1] );
		$rows[] = array(
			'label'  => __( 'جدار الحماية', 'qafesheh' ),
			'state'  => qaf_opt( 'waf', 1 ) ? 'ok' : 'warn',
			'detail' => qaf_opt( 'waf', 1 ) ? sprintf( /* translators: %d: count */ _n( 'تم صدّ %d محاولة هذا الأسبوع', 'تم صدّ %d محاولة هذا الأسبوع', qaf_waf_count(), 'qafesheh' ), qaf_waf_count() ) : __( 'متوقف', 'qafesheh' ),
		);
		$rows[] = array(
			'label'  => __( 'تنبيهات أمنية بالإيميل', 'qafesheh' ),
			'state'  => qaf_opt( 'admin_alerts', 1 ) ? 'ok' : 'warn',
			'detail' => qaf_opt( 'admin_alerts', 1 ) ? __( 'المدراء الجدد والدخول من أجهزة جديدة', 'qafesheh' ) : __( 'متوقف', 'qafesheh' ),
		);
		return $rows;
	},
	5
);
