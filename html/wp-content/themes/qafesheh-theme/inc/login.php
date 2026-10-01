<?php
/**
 * The login screen, in the Qafesheh Group identity.
 *
 * A split screen: the craft on one side (the atelier photograph under an ink veil, the seal,
 * the group statement and its three houses), a calm paper card on the other. Same experience
 * on the private login address, lost-password and reset screens. Mobile: the photograph becomes
 * a band above the card.
 *
 * Loaded on every request (the login screen is not is_admin()).
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

/**
 * Fonts for the login screen.
 */
function qaf_login_assets() {
	wp_enqueue_style( 'qaf-login-fonts', QAF_URI . 'assets/css/qaf-fonts.css', array(), QAF_VERSION );
}
add_action( 'login_enqueue_scripts', 'qaf_login_assets' );

/**
 * Logo link + text.
 */
add_filter( 'login_headerurl', static fn() => home_url( '/' ) );
add_filter( 'login_headertext', static fn() => (string) ( qaf_opt_i18n( 'brand_name' ) ? qaf_opt_i18n( 'brand_name' ) : get_bloginfo( 'name' ) ) );

/**
 * The visual side of the split screen.
 */
function qaf_login_aside() {
	$en    = 0 === strpos( determine_locale(), 'en' );
	$logo  = (string) qaf_opt( 'logo' );
	$logo  = $logo ? ( 0 === strpos( $logo, 'http' ) ? $logo : QAF_URI . ltrim( $logo, '/' ) ) : QAF_URI . 'assets/images/logos/logo-qafesheh-seal.png';
	$names = array();
	foreach ( get_posts( array( 'post_type' => 'qaf_company', 'posts_per_page' => 3, 'orderby' => 'menu_order', 'order' => 'ASC' ) ) as $c ) {
		$names[] = $c->post_title;
	}
	if ( ! $names ) {
		$names = $en ? array( 'Qafesha', 'Al Jazi', 'Makarem' ) : array( 'قفيشة', 'الجازي', 'مكارم' );
	}
	?>
	<aside class="qaf-login-aside" aria-hidden="true">
		<div class="qaf-login-aside__veil"></div>
		<div class="qaf-login-aside__body">
			<img class="qaf-login-aside__seal" src="<?php echo esc_url( $logo ); ?>" alt="">
			<p class="qaf-login-aside__eyebrow">EST. 1968 — AMMAN, JORDAN</p>
			<p class="qaf-login-aside__title"><?php echo $en ? 'A Jordanian industrial group<br><em>in luxury heritage fashion</em>' : 'مجموعةٌ صناعية أردنية<br><em>في الأزياء التراثية الفاخرة</em>'; // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
			<span class="qaf-login-aside__rule"></span>
			<p class="qaf-login-aside__houses"><?php echo esc_html( implode( '  ·  ', $names ) ); ?></p>
		</div>
		<p class="qaf-login-aside__foot">© <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php echo esc_html( $en ? 'Qafesheh Industrial Group' : 'مجموعة قفيشة الصناعية' ); ?></p>
	</aside>
	<?php
}
add_action( 'login_header', 'qaf_login_aside' );

/**
 * A greeting above the form (only on the sign-in view, never on top of WordPress's own messages).
 *
 * @param string $message Message.
 * @return string
 */
function qaf_login_greeting( $message ) {
	$action = isset( $_REQUEST['action'] ) ? sanitize_key( wp_unslash( $_REQUEST['action'] ) ) : 'login'; // phpcs:ignore WordPress.Security.NonceVerification
	$en     = 0 === strpos( determine_locale(), 'en' );
	$copy   = array(
		'login'        => $en ? array( 'Welcome back', 'Qafesheh Group control panel' ) : array( 'أهلاً بعودتك', 'لوحة تحكم مجموعة قفيشة' ),
		'lostpassword' => $en ? array( 'Forgot your password?', 'Enter your email and we’ll send you a link.' ) : array( 'نسيت كلمة المرور؟', 'اكتب بريدك الإلكتروني ويوصلك رابط لتعيين كلمة جديدة.' ),
		'rp'           => $en ? array( 'A new password', 'Choose a strong password you’ll remember.' ) : array( 'كلمة مرور جديدة', 'اختر كلمة مرور قوية وسهلة التذكّر عليك.' ),
		'resetpass'    => $en ? array( 'A new password', 'Choose a strong password you’ll remember.' ) : array( 'كلمة مرور جديدة', 'اختر كلمة مرور قوية وسهلة التذكّر عليك.' ),
	);
	if ( ! isset( $copy[ $action ] ) ) {
		return $message;
	}
	return '<div class="qaf-login-hello"><h2>' . esc_html( $copy[ $action ][0] ) . '</h2><p>' . esc_html( $copy[ $action ][1] ) . '</p></div>' . $message;
}
add_filter( 'login_message', 'qaf_login_greeting' );

/**
 * Small footer under the card.
 */
function qaf_login_footer() {
	echo '<p class="qaf-login-credit">' . esc_html( 0 === strpos( determine_locale(), 'en' ) ? 'Design & development' : 'تصميم وتطوير' ) . ' <a href="https://qb.studio" target="_blank" rel="noopener">QB Studio</a></p>';
}
add_action( 'login_footer', 'qaf_login_footer' );

/**
 * Styles — printed last in <head> so they win over core's login.css.
 */
function qaf_login_styles() {
	$logo = (string) qaf_opt( 'logo' );
	$logo = $logo ? ( 0 === strpos( $logo, 'http' ) ? $logo : QAF_URI . ltrim( $logo, '/' ) ) : QAF_URI . 'assets/images/logos/logo-qafesheh-seal.png';
	$bg   = QAF_URI . 'assets/import/fur-stitching.jpg';
	?>
	<style id="qaf-login">
		:root { --g: #B8912F; --g2: #D9B85C; --gd: #8C6B1F; --ink: #0F0E0C; --stone: #F2EEE7; --paper: #FFFCF6; --line: #D6CDBD; --body: #463f36; --muted: #756B5E; }
		html, body.login { min-height: 100%; }
		body.login { background: var(--stone); font-family: 'IBM Plex Sans Arabic', system-ui, 'Segoe UI', Tahoma, sans-serif; color: var(--body); display: block; }
		body.login * { font-family: inherit; box-sizing: border-box; }
		body.login .dashicons, body.login .dashicons::before { font-family: dashicons !important; }

		/* ---------- Visual side */
		.qaf-login-aside { box-sizing: border-box; position: fixed; inset-block: 0; inset-inline-end: 0; width: 52vw; overflow: hidden; background: #14130f url('<?php echo esc_url( $bg ); ?>') 34% center / cover no-repeat; display: flex; flex-direction: column; justify-content: flex-end; padding: 0 clamp(36px, 5vw, 80px) clamp(36px, 5vh, 64px); color: #efe8da; isolation: isolate; }
		.qaf-login-aside__veil { position: absolute; inset: 0; z-index: -1; background: linear-gradient(180deg, rgba(15,14,12,.35) 0%, rgba(15,14,12,.55) 45%, rgba(15,14,12,.94) 100%), radial-gradient(90% 60% at 80% 100%, rgba(184,145,47,.28), rgba(184,145,47,0) 70%); }
		.qaf-login-aside::after { content: ""; position: absolute; inset: 22px; z-index: -1; border: 1px solid rgba(217,184,92,.28); pointer-events: none; }
		.qaf-login-aside__body { display: flex; flex-direction: column; align-items: flex-start; gap: 16px; max-width: 520px; animation: qafUp .9s cubic-bezier(.2,.7,.2,1) both; }
		.qaf-login-aside__seal { width: 78px; height: 78px; border-radius: 50%; box-shadow: 0 10px 30px -10px rgba(0,0,0,.7); }
		.qaf-login-aside__eyebrow { margin: 6px 0 0; font-family: 'Libre Baskerville', Georgia, serif !important; font-size: 11px; letter-spacing: .32em; color: var(--g2); direction: ltr; }
		.qaf-login-aside__title { margin: 0; font-family: 'Noto Naskh Arabic', 'Libre Baskerville', serif !important; font-size: clamp(30px, 2.7vw, 44px); line-height: 1.45; font-weight: 700; color: #fff; }
		.qaf-login-aside__title em { font-style: normal; font-weight: 400; color: #e9dcc0; }
		.qaf-login-aside__rule { display: block; width: 96px; height: 2px; background: var(--g); }
		.qaf-login-aside__houses { margin: 0; font-size: 14px; letter-spacing: .06em; color: #cfc3a8; }
		.qaf-login-aside__foot { position: absolute; inset-block-start: 40px; inset-inline-start: clamp(36px, 5vw, 80px); margin: 0; font-size: 11px; color: rgba(239,232,218,.6); letter-spacing: .04em; }

		/* ---------- Form side */
		#login { position: relative; box-sizing: border-box; width: 48vw; max-width: none; margin: 0; padding: 11vh max(32px, calc((48vw - 400px) / 2)) 90px; animation: qafUp .7s .1s cubic-bezier(.2,.7,.2,1) both; }
		#login h1 a { background: url('<?php echo esc_url( $logo ); ?>') center / contain no-repeat; width: 72px; height: 72px; margin: 0 0 22px; margin-inline-start: 0; border-radius: 50%; box-shadow: 0 10px 26px -12px rgba(15,14,12,.55); }
		.login h1 { text-align: start; }
		.qaf-login-hello { margin: 0 0 20px; }
		.qaf-login-hello h2 { margin: 0 0 6px; font-family: 'Noto Naskh Arabic', serif !important; font-size: 30px; line-height: 1.35; font-weight: 700; color: var(--ink); }
		.qaf-login-hello p { margin: 0; font-size: 14px; color: var(--muted); }
		body.login form { position: relative; margin: 0; padding: 30px 28px 26px; background: var(--paper); border: 1px solid var(--line); border-radius: 22px; box-shadow: 0 1px 2px rgba(15,14,12,.04), 0 34px 60px -38px rgba(15,14,12,.45); overflow: hidden; }
		body.login form::before { content: ""; position: absolute; inset-inline: 0; inset-block-start: 0; height: 3px; background: linear-gradient(90deg, var(--gd), var(--g2), var(--gd)); }
		body.login label { color: var(--ink); font-size: 13px; font-weight: 500; margin-bottom: 6px; display: inline-block; }
		body.login input[type=text], body.login input[type=password], body.login input[type=email] { min-height: 52px; margin: 0 0 18px; padding: 6px 16px; border: 1px solid var(--line); border-radius: 12px; background: #F7F3EC; font-size: 15px; color: var(--ink); box-shadow: none; transition: border-color .2s, box-shadow .2s, background-color .2s; }
		body.login input[type=text]:focus, body.login input[type=password]:focus, body.login input[type=email]:focus { border-color: var(--g); background: #fff; box-shadow: 0 0 0 3px rgba(184,145,47,.18); outline: 0; }
		body.login .wp-pwd { margin-bottom: 0; }
		body.login .button.wp-hide-pw { height: 52px; min-height: 52px; color: var(--muted); }
		body.login .button.wp-hide-pw:focus { box-shadow: none; border-color: transparent; }
		body.login .forgetmenot { float: none; margin: 2px 0 16px; }
		body.login .forgetmenot label { font-size: 13px; color: var(--body); font-weight: 400; }
		body.login input[type=checkbox] { border-color: #bfb29a; border-radius: 4px; }
		body.login input[type=checkbox]:checked { background: var(--g); border-color: var(--g); }
		body.login input[type=checkbox]:checked::before { content: ""; background: none; filter: none; width: 6px; height: 10px; margin: 1px 5px; border: solid #fff; border-width: 0 2px 2px 0; transform: rotate(45deg); }
		body.login .submit { display: block; clear: both; }
		body.login.wp-core-ui .button-primary { float: none; width: 100%; min-height: 52px; margin: 0; padding: 0 20px; border: 0; border-radius: 12px; background: var(--g); color: var(--ink); font-size: 15px; font-weight: 700; letter-spacing: .01em; text-shadow: none; box-shadow: 0 16px 28px -16px rgba(140,107,31,.8); transition: background-color .25s, color .25s, transform .25s; }
		body.login.wp-core-ui .button-primary:hover, body.login.wp-core-ui .button-primary:focus { background: var(--ink); color: var(--g2); transform: translateY(-1px); box-shadow: 0 16px 28px -16px rgba(15,14,12,.8); }
		body.login #nav, body.login #backtoblog { margin: 18px 0 0; padding: 0; text-align: start; font-size: 13px; }
		body.login #backtoblog { margin-top: 8px; }
		body.login #nav a, body.login #backtoblog a { color: var(--muted); text-decoration: none; transition: color .2s; }
		body.login #nav a:hover, body.login #backtoblog a:hover { color: var(--gd); }
		body.login .message, body.login .notice, body.login #login_error { margin: 0 0 18px; padding: 12px 16px; border: 0; border-inline-start: 3px solid var(--g); border-radius: 12px; background: #FBF6EA; box-shadow: none; color: var(--ink); font-size: 13px; }
		body.login #login_error, body.login .notice-error { border-inline-start-color: #A6402A; background: #FBEEEA; }
		body.login .privacy-policy-page-link, body.login .language-switcher { display: none; }
		body.login #pass-strength-result { border-radius: 10px; margin-bottom: 14px; }
		body.login .pw-weak { margin-bottom: 14px; }
		.qaf-login-credit { position: fixed; inset-block-end: 22px; inset-inline-start: 0; width: 48vw; margin: 0; text-align: center; font-size: 11px; color: var(--muted); }
		.qaf-login-credit a { color: var(--gd); text-decoration: none; }
		@keyframes qafUp { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: none; } }
		@media (prefers-reduced-motion: reduce) { .qaf-login-aside__body, #login { animation: none; } }

		/* ---------- Tablet / phone: the photograph becomes a band */
		@media (max-width: 900px) {
			.qaf-login-aside { position: relative; width: 100%; height: 300px; inset: auto; padding: 0 24px 28px; }
			.qaf-login-aside::after { inset: 12px; }
			.qaf-login-aside__seal { width: 56px; height: 56px; }
			.qaf-login-aside__title { font-size: 24px; }
			.qaf-login-aside__houses, .qaf-login-aside__foot { display: none; }
			#login { width: auto; max-width: 440px; margin: 0 auto; padding: 28px 20px 30px; }
			#login h1 { display: none; }
			.qaf-login-credit { position: static; width: auto; text-align: center; padding-bottom: 24px; }
		}
	</style>
	<?php
}
add_action( 'login_head', 'qaf_login_styles', 99 );
