<?php
/**
 * Security — built into the theme, no extra plugin.
 *
 * · Hidden login: wp-login.php answers only on a private address (default /qb-dash); the old
 *   addresses stop existing for visitors, and every link WordPress generates is rewritten.
 * · Hardening: login throttling, generic login errors, no user enumeration, XML-RPC off,
 *   no version fingerprint, no file editing from the dashboard, security headers.
 * · Team: add a colleague with one short form (محرّر محتوى / موظف متجر) — no WordPress user screens.
 *
 * Emergency: put `define( 'QAF_NO_HIDE_LOGIN', true );` in wp-config.php to bring wp-login.php back.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

/* ================================================= Hidden login */

/**
 * The private login slug.
 *
 * @return string
 */
function qaf_login_slug() {
	$slug = sanitize_title( (string) qaf_opt( 'login_slug' ) );
	return $slug ? $slug : 'qb-dash';
}

/**
 * Is hiding active?
 *
 * @return bool
 */
function qaf_hide_login() {
	return ! defined( 'QAF_NO_HIDE_LOGIN' ) && qaf_opt( 'hide_login' ) && ! is_multisite();
}

/**
 * The login URL on the private slug.
 *
 * @param string $scheme Scheme.
 * @return string
 */
function qaf_login_url( $scheme = 'login' ) {
	return home_url( '/' . qaf_login_slug() . '/', $scheme );
}

/**
 * Path of the current request, relative to the site root.
 *
 * @return string
 */
function qaf_request_path() {
	$path = (string) wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '', PHP_URL_PATH );
	$home = (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH );
	if ( $home && '/' !== $home && 0 === strpos( $path, $home ) ) {
		$path = substr( $path, strlen( $home ) - 1 );
	}
	return trim( $path, '/' );
}

/**
 * Catch the private slug, and make the old login address disappear.
 */
function qaf_login_guard() {
	if ( ! qaf_hide_login() || wp_doing_cron() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
		return;
	}
	$path = qaf_request_path();
	$file = basename( $path );

	/* Our private address → hand the request to WordPress's own login screen. */
	if ( $path === qaf_login_slug() ) {
		$GLOBALS['pagenow']        = 'wp-login.php'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		$GLOBALS['qaf_login_here'] = true;
		return;
	}
	/* Old addresses: gone. */
	if ( in_array( $file, array( 'wp-login.php', 'wp-register.php' ), true ) ) {
		$action = isset( $_REQUEST['action'] ) ? sanitize_key( wp_unslash( $_REQUEST['action'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		/* Logout, password reset and post-password keep working — they carry their own tokens. */
		if ( in_array( $action, array( 'logout', 'postpass', 'rp', 'resetpass', 'lostpassword', 'retrievepassword' ), true ) ) {
			return;
		}
		$GLOBALS['qaf_login_404'] = true;
	}
}
add_action( 'after_setup_theme', 'qaf_login_guard', 1 );

/**
 * Serve the login screen on the private slug, or a 404 for the old one.
 */
function qaf_login_serve() {
	if ( ! empty( $GLOBALS['qaf_login_404'] ) ) {
		qaf_send_404();
	}
	if ( empty( $GLOBALS['qaf_login_here'] ) ) {
		return;
	}
	if ( is_user_logged_in() && ! isset( $_REQUEST['action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		wp_safe_redirect( admin_url( 'admin.php?page=qaf-home' ) );
		exit;
	}
	global $error, $interim_login, $action, $user_login, $user, $redirect_to; // phpcs:ignore
	require_once ABSPATH . 'wp-login.php';
	exit;
}
add_action( 'wp_loaded', 'qaf_login_serve', 1 );

/**
 * A real 404 (nothing here to discover).
 */
function qaf_send_404() {
	global $wp_query;
	status_header( 404 );
	nocache_headers();
	if ( $wp_query instanceof WP_Query ) {
		$wp_query->set_404();
	}
	$template = get_404_template();
	if ( $template ) {
		include $template;
	}
	exit;
}

/**
 * /wp-admin for visitors who are not logged in: 404 instead of a redirect that reveals the login.
 */
function qaf_admin_guard() {
	if ( ! qaf_hide_login() || is_user_logged_in() || wp_doing_ajax() || wp_doing_cron() ) {
		return;
	}
	$file = basename( qaf_request_path() );
	if ( in_array( $file, array( 'admin-ajax.php', 'admin-post.php' ), true ) ) {
		return;
	}
	if ( is_admin() ) {
		qaf_send_404();
	}
}
add_action( 'wp_loaded', 'qaf_admin_guard', 2 );

/**
 * Rewrite every login URL WordPress builds.
 *
 * @param string $url    URL.
 * @param string $path   Path.
 * @param string $scheme Scheme.
 * @return string
 */
function qaf_filter_login_url( $url, $path = '', $scheme = null ) {
	if ( ! qaf_hide_login() || false === strpos( (string) $url, 'wp-login.php' ) ) {
		return $url;
	}
	$query = (string) wp_parse_url( $url, PHP_URL_QUERY );
	$args  = array();
	if ( $query ) {
		wp_parse_str( $query, $args );
	}
	/* Password-protected posts and logout keep the core endpoint (they carry nonces). */
	if ( in_array( $args['action'] ?? '', array( 'postpass', 'logout', 'rp', 'resetpass' ), true ) ) {
		return $url;
	}
	return add_query_arg( $args, qaf_login_url( $scheme ? $scheme : 'login' ) );
}
add_filter( 'site_url', 'qaf_filter_login_url', 10, 3 );
add_filter( 'network_site_url', 'qaf_filter_login_url', 10, 3 );
add_filter( 'login_url', 'qaf_filter_login_url', 10, 3 );
add_filter( 'lostpassword_url', 'qaf_filter_login_url', 10, 3 );
add_filter( 'register_url', 'qaf_filter_login_url', 10, 3 );
add_filter( 'wp_redirect', 'qaf_filter_login_url', 10, 2 );

/* ================================================= Hardening */

/**
 * Client IP for the login throttle (the careers module already defines one — reuse it).
 *
 * @return string
 */
function qaf_login_ip() {
	if ( function_exists( 'qaf_client_ip' ) ) {
		return (string) qaf_client_ip();
	}
	return preg_replace( '/[^0-9a-f:.]/i', '', (string) ( $_SERVER['REMOTE_ADDR'] ?? '' ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
}

/**
 * Count a failed login.
 */
function qaf_login_failed() {
	if ( ! qaf_opt( 'limit_logins' ) ) {
		return;
	}
	$key = 'qaf_lf_' . md5( qaf_login_ip() );
	set_transient( $key, (int) get_transient( $key ) + 1, 15 * MINUTE_IN_SECONDS );
	$log = (array) get_option( 'qaf_login_failures', array() );
	$log[] = array( 'ip' => qaf_login_ip(), 't' => time() );
	update_option( 'qaf_login_failures', array_slice( array_filter( $log, static fn( $r ) => $r['t'] > time() - DAY_IN_SECONDS ), -200 ), false );
}
add_action( 'wp_login_failed', 'qaf_login_failed' );

/**
 * Block after 6 failures in 15 minutes.
 *
 * @param WP_User|WP_Error|null $user User.
 * @return WP_User|WP_Error|null
 */
function qaf_login_throttle( $user ) {
	if ( ! qaf_opt( 'limit_logins' ) ) {
		return $user;
	}
	$tries = (int) get_transient( 'qaf_lf_' . md5( qaf_login_ip() ) );
	if ( $tries >= 6 ) {
		return new WP_Error( 'qaf_throttled', '<strong>محاولات كثيرة.</strong> انتظر ١٥ دقيقة وحاول مرة ثانية.' );
	}
	return $user;
}
add_filter( 'authenticate', 'qaf_login_throttle', 30 );

/**
 * Clear the counter on success.
 */
function qaf_login_ok() {
	delete_transient( 'qaf_lf_' . md5( qaf_login_ip() ) );
}
add_action( 'wp_login', 'qaf_login_ok' );

/**
 * Generic login error (never reveal whether the user exists).
 *
 * @param string $error Error.
 * @return string
 */
function qaf_login_errors( $error ) {
	if ( ! qaf_opt( 'hide_login_errors' ) ) {
		return $error;
	}
	if ( false !== strpos( (string) $error, 'qaf_throttled' ) || false !== strpos( (string) $error, 'محاولات كثيرة' ) ) {
		return $error;
	}
	return 'بيانات الدخول غير صحيحة.';
}
add_filter( 'login_errors', 'qaf_login_errors', 99 );

/**
 * No user enumeration: ?author=N, and the REST users route for visitors.
 */
function qaf_block_enumeration() {
	if ( ! qaf_opt( 'block_enum' ) || is_admin() || is_user_logged_in() ) {
		return;
	}
	if ( isset( $_GET['author'] ) && ! is_author() ) { // phpcs:ignore WordPress.Security.NonceVerification
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'qaf_block_enumeration', 1 );

/**
 * REST: the users route needs a logged-in user.
 *
 * @param mixed           $result  Result.
 * @param mixed           $server  Server.
 * @param WP_REST_Request $request Request.
 * @return mixed
 */
function qaf_rest_users( $result, $server, $request ) {
	if ( ! qaf_opt( 'block_enum' ) || is_user_logged_in() ) {
		return $result;
	}
	if ( 0 === strpos( (string) $request->get_route(), '/wp/v2/users' ) ) {
		return new WP_Error( 'rest_forbidden', 'غير مسموح.', array( 'status' => 401 ) );
	}
	return $result;
}
add_filter( 'rest_pre_dispatch', 'qaf_rest_users', 10, 3 );

/**
 * XML-RPC off (nothing on this site uses it) and its file blocked.
 */
function qaf_block_xmlrpc() {
	if ( ! qaf_opt( 'disable_xmlrpc' ) ) {
		return;
	}
	add_filter( 'xmlrpc_enabled', '__return_false' );
	add_filter( 'wp_headers', static function ( $h ) {
		unset( $h['X-Pingback'] );
		return $h;
	} );
	if ( 'xmlrpc.php' === basename( qaf_request_path() ) ) {
		status_header( 403 );
		exit;
	}
}
add_action( 'after_setup_theme', 'qaf_block_xmlrpc', 2 );

/**
 * No version fingerprint.
 */
function qaf_no_fingerprint() {
	remove_action( 'wp_head', 'wp_generator' );
	add_filter( 'the_generator', '__return_empty_string' );
}
add_action( 'init', 'qaf_no_fingerprint' );

/**
 * No editing theme/plugin files from the dashboard.
 */
function qaf_no_file_edit() {
	if ( qaf_opt( 'disallow_file_edit' ) && ! defined( 'DISALLOW_FILE_EDIT' ) ) {
		define( 'DISALLOW_FILE_EDIT', true );
	}
}
add_action( 'init', 'qaf_no_file_edit', 1 );

/**
 * Security headers.
 */
function qaf_security_headers() {
	if ( ! qaf_opt( 'security_headers' ) || is_admin() ) {
		return;
	}
	header( 'X-Content-Type-Options: nosniff' );
	header( 'X-Frame-Options: SAMEORIGIN' );
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );
	header( 'Permissions-Policy: geolocation=(), microphone=(), camera=(), interest-cohort=()' );
	header( 'X-XSS-Protection: 0' );
	if ( is_ssl() ) {
		header( 'Strict-Transport-Security: max-age=31536000; includeSubDomains' );
	}
}
add_action( 'send_headers', 'qaf_security_headers' );

/**
 * CVs and complaint attachments are private: never reachable as an attachment page.
 *
 * @param WP_Query $q Query.
 */
function qaf_hide_private_files( $q ) {
	if ( is_admin() || ! $q->is_main_query() || ! $q->is_attachment() ) {
		return;
	}
	$id = (int) $q->get( 'attachment_id' );
	if ( $id && 'private' === get_post_status( $id ) && ! current_user_can( 'edit_posts' ) ) {
		qaf_send_404();
	}
}
add_action( 'pre_get_posts', 'qaf_hide_private_files' );

/* ================================================= Team */

/**
 * Roles a team member can get from this screen.
 *
 * @return array
 */
function qaf_team_roles() {
	return array(
		'editor' => array( 'محرّر المحتوى', 'الأخبار والوظائف والصفحات والرسائل وطلبات التوظيف — بدون إعدادات الموقع.' ),
		'author' => array( 'محرّر الأخبار', 'ينشر ويعدّل أخباره ومقالاته فقط.' ),
	);
}

/**
 * Team screen.
 */
function qaf_team_menu() {
	add_submenu_page( 'qaf-options', 'الفريق', 'الفريق', 'promote_users', 'qaf-team', 'qaf_render_team_page' );
}
add_action( 'admin_menu', 'qaf_team_menu', 21 );

/**
 * Add / remove a team member.
 */
function qaf_team_action() {
	if ( ! current_user_can( 'promote_users' ) ) {
		wp_die( 'Not allowed.' );
	}
	check_admin_referer( 'qaf_team' );
	$msg   = '';
	$roles = qaf_team_roles();
	$do    = isset( $_POST['do'] ) ? sanitize_key( wp_unslash( $_POST['do'] ) ) : ( isset( $_GET['do'] ) ? sanitize_key( wp_unslash( $_GET['do'] ) ) : '' );
	if ( 'add' === $do ) {
		$name  = sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) );
		$email = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
		$role  = isset( $roles[ $_POST['role'] ?? '' ] ) ? sanitize_key( wp_unslash( $_POST['role'] ) ) : 'editor';
		if ( ! $name || ! is_email( $email ) ) {
			$msg = 'اكتب الاسم وبريد إلكتروني صحيح.';
		} elseif ( email_exists( $email ) ) {
			$msg = 'في حساب بنفس البريد الإلكتروني.';
		} else {
			$login = sanitize_user( current( explode( '@', $email ) ), true );
			$login = username_exists( $login ) ? $login . wp_rand( 10, 99 ) : $login;
			$id    = wp_insert_user(
				array(
					'user_login'   => $login,
					'user_email'   => $email,
					'user_pass'    => wp_generate_password( 20, true, true ),
					'display_name' => $name,
					'first_name'   => $name,
					'role'         => $role,
				)
			);
			if ( is_wp_error( $id ) ) {
				$msg = $id->get_error_message();
			} else {
				update_user_meta( $id, 'qaf_dev_mode', '0' );
				wp_new_user_notification( $id, null, 'user' );
				$msg = 'تم إنشاء الحساب، ووصله إيميل فيه رابط لتعيين كلمة المرور.';
			}
		}
	} elseif ( 'remove' === $do ) {
		$id = isset( $_GET['user'] ) ? absint( $_GET['user'] ) : 0;
		$u  = $id ? get_userdata( $id ) : null;
		if ( $u && $id !== get_current_user_id() && ! user_can( $u, 'manage_options' ) ) {
			require_once ABSPATH . 'wp-admin/includes/user.php';
			wp_delete_user( $id, get_current_user_id() );
			$msg = 'تم حذف الحساب، وكل ما أنشأه انتقل لحسابك.';
		} else {
			$msg = 'ما بنقدر نحذف هذا الحساب من هون.';
		}
	}
	set_transient( 'qaf_team_msg', $msg, 60 );
	wp_safe_redirect( admin_url( 'admin.php?page=qaf-team' ) );
	exit;
}
add_action( 'admin_post_qaf_team', 'qaf_team_action' );

/**
 * Team screen output.
 */
function qaf_render_team_page() {
	$roles = qaf_team_roles();
	$msg   = (string) get_transient( 'qaf_team_msg' );
	delete_transient( 'qaf_team_msg' );
	$users = get_users( array( 'orderby' => 'display_name', 'number' => 100 ) );
	?>
	<div class="wrap qaf-team" dir="rtl">
		<h1>الفريق</h1>
		<p class="qaf-team__lead">أضف زميلاً بحساب خاص فيه — بدون مشاركة حسابك. يوصله إيميل ليعيّن كلمة المرور بنفسه.</p>
		<?php if ( $msg ) : ?>
			<div class="notice notice-info"><p><?php echo esc_html( $msg ); ?></p></div>
		<?php endif; ?>
		<div class="qaf-team__grid">
			<form class="qaf-team__form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="qaf_team">
				<input type="hidden" name="do" value="add">
				<?php wp_nonce_field( 'qaf_team' ); ?>
				<h2>إضافة عضو</h2>
				<p><label for="qaf-team-name">الاسم</label><input id="qaf-team-name" name="name" type="text" required placeholder="مثال: رهام"></p>
				<p><label for="qaf-team-email">البريد الإلكتروني</label><input id="qaf-team-email" name="email" type="email" required placeholder="name@qafesheh.com"></p>
				<p><label for="qaf-team-role">الصلاحية</label>
					<select id="qaf-team-role" name="role">
						<?php foreach ( $roles as $k => $r ) : ?>
							<option value="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $r[0] ); ?></option>
						<?php endforeach; ?>
					</select>
				</p>
				<ul class="qaf-team__roles">
					<?php foreach ( $roles as $r ) : ?>
						<li><b><?php echo esc_html( $r[0] ); ?>:</b> <?php echo esc_html( $r[1] ); ?></li>
					<?php endforeach; ?>
				</ul>
				<button type="submit" class="button button-primary">إنشاء الحساب وإرسال الدعوة</button>
			</form>
			<div class="qaf-team__list">
				<h2>الحسابات الحالية <span class="qaf-team__count"><?php echo esc_html( (string) count( $users ) ); ?></span></h2>
				<ul class="qaf-team__people">
					<?php foreach ( $users as $u ) : ?>
						<?php
						$role  = (string) ( $u->roles[0] ?? '' );
						$label = $roles[ $role ][0] ?? ( 'administrator' === $role ? 'مدير كامل' : $role );
						$init  = mb_substr( trim( $u->display_name ), 0, 1 );
						?>
						<li>
							<span class="qaf-team__avatar"><?php echo esc_html( $init ); ?></span>
							<span class="qaf-team__who"><b><?php echo esc_html( $u->display_name ); ?><?php echo get_current_user_id() === $u->ID ? ' <small>(أنت)</small>' : ''; ?></b><span><?php echo esc_html( $u->user_email ); ?></span></span>
							<span class="qaf-team__role is-<?php echo esc_attr( $role ); ?>"><?php echo esc_html( $label ); ?></span>
							<?php if ( get_current_user_id() !== $u->ID && ! user_can( $u, 'manage_options' ) ) : ?>
								<a class="qaf-team__del" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=qaf_team&do=remove&user=' . $u->ID ), 'qaf_team' ) ); ?>" onclick="return confirm('حذف هذا الحساب؟ كل ما أنشأه بينتقل لحسابك.')">حذف</a>
							<?php else : ?>
								<span class="qaf-team__lock" title="يُدار من QB Studio"><span class="dashicons dashicons-lock"></span></span>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
				<p class="description">حسابات «مدير كامل» تُدار من QB Studio لأسباب أمنية.</p>
			</div>
		</div>
	</div>
	<style>
		.qaf-team{max-width:1100px}
		.qaf-team__lead{max-width:640px;color:#756B5E;font-size:14px}
		.qaf-team__grid{display:grid;grid-template-columns:360px minmax(0,1fr);gap:20px;align-items:start;margin-top:18px}
		.qaf-team__form,.qaf-team__list{position:relative;background:#FFFCF6;border:1px solid #D6CDBD;border-radius:20px;padding:22px 24px;overflow:hidden}
		.qaf-team__form::before{content:"";position:absolute;inset-inline:0;top:0;height:3px;background:linear-gradient(90deg,#8C6B1F,#D9B85C,#8C6B1F)}
		.qaf-team__form h2,.qaf-team__list h2{margin:0 0 16px;font-size:17px;display:flex;align-items:center;gap:8px}
		.qaf-team__form h2::before,.qaf-team__list h2::before{content:"";width:7px;height:7px;background:#B8912F;transform:rotate(45deg)}
		.qaf-team__form p{margin:0 0 14px}
		.qaf-team__form label{display:block;font-size:13px;font-weight:600;margin-bottom:6px;color:#0F0E0C}
		.qaf-team__form input,.qaf-team__form select{width:100%;min-height:44px;border-radius:12px}
		.qaf-team__roles{margin:4px 0 18px;padding:12px 14px;border-radius:12px;background:#EFE8DA;font-size:12px;line-height:1.9;list-style:none}
		.qaf-team__form .button-primary{width:100%;min-height:46px}
		.qaf-team__count{display:inline-block;min-width:22px;padding:0 8px;border-radius:99px;background:#0F0E0C;color:#D9B85C;font-size:11px;line-height:22px;text-align:center}
		.qaf-team__people{margin:0;list-style:none}
		.qaf-team__people li{display:flex;align-items:center;gap:14px;padding:12px 0;border-bottom:1px dashed #E3DACB}
		.qaf-team__people li:last-child{border-bottom:0}
		.qaf-team__avatar{flex-shrink:0;width:42px;height:42px;border-radius:50%;background:#0F0E0C;color:#D9B85C;display:flex;align-items:center;justify-content:center;font-size:17px;font-weight:700}
		.qaf-team__who{flex:1;min-width:0;display:flex;flex-direction:column;gap:2px}
		.qaf-team__who b{font-size:14px;color:#0F0E0C}
		.qaf-team__who small{color:#756B5E;font-weight:400}
		.qaf-team__who span{font-size:12px;color:#756B5E}
		.qaf-team__role{padding:3px 12px;border-radius:99px;font-size:11px;font-weight:600;background:#EFE8DA;color:#463f36;white-space:nowrap}
		.qaf-team__role.is-administrator{background:#0F0E0C;color:#D9B85C}
		.qaf-team__role.is-editor{background:#F6EBCB;color:#7a5a12}
		.qaf-team__del{font-size:12px;color:#A6402A;text-decoration:none}
		.qaf-team__lock{color:#b7ab95}
		.qaf-team__list .description{margin-top:14px}
		@media(max-width:900px){.qaf-team__grid{grid-template-columns:minmax(0,1fr)}}
	</style>
	<?php
}

/* ================================================= Security status (launch centre) */

/**
 * Security checks shown in the launch centre.
 *
 * @return array[] [ label, state, detail ]
 */
function qaf_security_checks() {
	$out   = array();
	$add   = static function ( $label, $state, $detail = '' ) use ( &$out ) {
		$out[] = compact( 'label', 'state', 'detail' );
	};
	$add( 'شهادة SSL (https)', is_ssl() || 0 === strpos( home_url(), 'https' ) ? 'ok' : 'todo', home_url( '/' ) );
	$add( 'رابط الدخول الخاص', qaf_hide_login() ? 'ok' : 'warn', qaf_hide_login() ? home_url( '/' . qaf_login_slug() . '/' ) : 'wp-login.php مكشوف' );
	$add( 'تحديد محاولات الدخول', qaf_opt( 'limit_logins' ) ? 'ok' : 'warn', sprintf( '%d محاولة فاشلة آخر 24 ساعة', count( array_filter( (array) get_option( 'qaf_login_failures', array() ), static fn( $r ) => ( $r['t'] ?? 0 ) > time() - DAY_IN_SECONDS ) ) ) );
	$add( 'XML-RPC', qaf_opt( 'disable_xmlrpc' ) ? 'ok' : 'warn', 'مغلق' );
	$add( 'تعديل الملفات من اللوحة', ( defined( 'DISALLOW_FILE_EDIT' ) && DISALLOW_FILE_EDIT ) ? 'ok' : 'warn', 'معطّل' );
	$admins = get_users( array( 'role' => 'administrator', 'fields' => array( 'user_login' ) ) );
	$weak   = array_filter( $admins, static fn( $u ) => in_array( strtolower( $u->user_login ), array( 'admin', 'administrator', 'qafesheh', 'qafesha' ), true ) );
	$add( 'أسماء حسابات المدراء', $weak ? 'warn' : 'ok', $weak ? 'يوجد حساب باسم متوقّع (admin) — يفضّل تغييره' : sprintf( '%d حساب مدير', count( $admins ) ) );
	if ( function_exists( 'get_core_updates' ) ) {
		$core = get_site_transient( 'update_core' );
		$plug = get_site_transient( 'update_plugins' );
		$n    = ( isset( $plug->response ) ? count( (array) $plug->response ) : 0 ) + ( isset( $core->updates[0] ) && 'upgrade' === $core->updates[0]->response ? 1 : 0 );
		$add( 'التحديثات', $n ? 'warn' : 'ok', $n ? sprintf( '%d تحديث بانتظار المراجعة', $n ) : 'كل شي محدّث' );
	}
	return (array) apply_filters( 'qaf_security_checks', $out );
}
