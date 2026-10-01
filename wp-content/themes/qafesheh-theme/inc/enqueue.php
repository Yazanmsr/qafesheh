<?php
/**
 * Assets: register everything, enqueue only where needed.
 * Versions come from filemtime so any edited file busts cache automatically.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

/**
 * Asset version = theme version + file mtime (static cache).
 *
 * @param string $rel Relative path.
 * @return string
 */
function qaf_asset_ver( $rel ) {
	static $cache = array();
	if ( ! isset( $cache[ $rel ] ) ) {
		$path          = QAF_DIR . ltrim( $rel, '/' );
		$cache[ $rel ] = QAF_VERSION . ( file_exists( $path ) ? '.' . filemtime( $path ) : '' );
	}
	return $cache[ $rel ];
}

/**
 * Register all front-end handles.
 */
function qaf_register_assets() {
	$css = array(
		'qaf-base'    => array( 'assets/css/base.css', array( 'qaf-fonts' ) ),
		'qaf-shell'   => array( 'assets/css/shell.css', array( 'qaf-base' ) ),
		'qaf-widgets' => array( 'assets/css/widgets.css', array( 'qaf-base' ) ),
		'qaf-blog'    => array( 'assets/css/blog.css', array( 'qaf-base' ) ),
		'qaf-companies' => array( 'assets/css/companies.css', array( 'qaf-base' ) ),
		'qaf-services'  => array( 'assets/css/services.css', array( 'qaf-base' ) ),
		'qaf-branches'  => array( 'assets/css/branches.css', array( 'qaf-base' ) ),
		'qaf-careers'   => array( 'assets/css/careers.css', array( 'qaf-base' ) ),
		'qaf-forms'     => array( 'assets/css/forms.css', array( 'qaf-base' ) ),
		'qaf-rtl'     => array( 'assets/css/rtl.css', array( 'qaf-base' ) ),
	);
	foreach ( $css as $handle => $def ) {
		wp_register_style( $handle, QAF_URI . $def[0], $def[1], qaf_asset_ver( $def[0] ) );
	}

	$js = array(
		'qaf-animations' => array( 'assets/js/animations.js', array() ),
		'qaf-app'        => array( 'assets/js/app.js', array( 'qaf-animations' ) ),
		'qaf-ui'         => array( 'assets/js/ui.js', array( 'qaf-app' ) ),
		'qaf-careers'    => array( 'assets/js/careers.js', array( 'qaf-app' ) ),
		'qaf-forms'      => array( 'assets/js/forms.js', array( 'qaf-app' ) ),
		'qaf-hero'       => array( 'assets/js/hero.js', array( 'qaf-animations' ) ),
		'qaf-article'    => array( 'assets/js/article.js', array( 'qaf-app' ) ),
	);
	foreach ( $js as $handle => $def ) {
		wp_register_script( $handle, QAF_URI . $def[0], $def[1], qaf_asset_ver( $def[0] ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
	}

	wp_add_inline_style( 'qaf-base', qaf_dynamic_css() );
	wp_localize_script( 'qaf-animations', 'QAFCfg', qaf_js_config() );
}
add_action( 'wp_enqueue_scripts', 'qaf_register_assets', 5 );
add_action( 'elementor/frontend/after_register_styles', 'qaf_register_assets', 5 );

/**
 * Conditional enqueue per view.
 */
function qaf_enqueue_assets() {
	wp_enqueue_style( 'qaf-base' );
	wp_enqueue_style( 'qaf-shell' );

	if ( qaf_view_uses_widgets() ) {
		wp_enqueue_style( 'qaf-widgets' );
	}
	$own_templates = is_singular( array( 'qaf_company', 'qaf_service', 'qaf_job' ) ) || is_post_type_archive( array( 'qaf_company', 'qaf_service', 'qaf_branch', 'qaf_job' ) );
	if ( ! is_front_page() && ! $own_templates && ( is_home() || is_archive() || is_search() || is_404() || is_singular( array( 'post', 'page', 'qaf_job' ) ) ) ) {
		wp_enqueue_style( 'qaf-blog' );
	}
	if ( is_singular( 'qaf_company' ) || is_post_type_archive( 'qaf_company' ) ) {
		wp_enqueue_style( 'qaf-companies' );
	}
	if ( is_singular( 'qaf_service' ) || is_post_type_archive( 'qaf_service' ) ) {
		wp_enqueue_style( 'qaf-services' );
	}
	if ( is_post_type_archive( 'qaf_branch' ) ) {
		wp_enqueue_style( 'qaf-branches' );
	}
	if ( is_singular( 'qaf_job' ) || is_post_type_archive( 'qaf_job' ) ) {
		wp_enqueue_style( 'qaf-careers' );
		wp_enqueue_style( 'qaf-forms' );
		wp_enqueue_script( 'qaf-forms' );
	}
	if ( is_post_type_archive( 'qaf_job' ) ) {
		wp_enqueue_script( 'qaf-careers' );
	}
	if ( qaf_view_has_forms() ) {
		wp_enqueue_style( 'qaf-forms' );
		wp_enqueue_script( 'qaf-forms' );
	}
	if ( is_singular( 'post' ) ) {
		wp_enqueue_script( 'qaf-article' );
	}
	if ( is_rtl() ) {
		wp_enqueue_style( 'qaf-rtl' );
	}

	wp_enqueue_script( 'qaf-animations' );
	wp_enqueue_script( 'qaf-app' );

	if ( qaf_view_uses_widgets() ) {
		wp_enqueue_script( 'qaf-ui' );
	}
	if ( is_front_page() || false !== strpos( (string) get_post_meta( get_queried_object_id(), '_elementor_data', true ), '"widgetType":"qb-hero"' ) ) {
		wp_enqueue_script( 'qaf-hero' );
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'qaf_enqueue_assets', 20 );

/**
 * Does the current view render QB sections?
 *
 * @return bool
 */
function qaf_view_uses_widgets() {
	static $uses = null;
	if ( null !== $uses ) {
		return $uses;
	}
	$uses = is_front_page() || is_post_type_archive( array( 'qaf_company', 'qaf_service', 'qaf_branch' ) ) || is_singular( array( 'qaf_company', 'qaf_service' ) );

	if ( ! $uses && is_singular() ) {
		$data = (string) get_post_meta( get_queried_object_id(), '_elementor_data', true );
		$uses = false !== strpos( $data, '"widgetType":"qb-' ) || (bool) qaf_page_recipe( get_queried_object_id() );
	}
	return (bool) apply_filters( 'qaf_view_uses_widgets', $uses );
}

/**
 * One JS config object.
 *
 * @return array
 */
function qaf_js_config() {
	return array(
		'ajax'   => admin_url( 'admin-ajax.php' ),
		'nonce'  => wp_create_nonce( 'qaf_nonce' ),
		'home'   => home_url( '/' ),
		'rtl'    => is_rtl(),
		'lang'   => qaf_current_lang(),
		'anim'   => array(
			'on'       => (bool) qaf_opt( 'anim_on' ),
			'parallax' => (bool) qaf_opt( 'hero_parallax' ),
			'count'    => (bool) qaf_opt( 'count_up' ),
		),
		'loader' => array(
			'on'   => qaf_loader_enabled(),
			'mode' => qaf_opt( 'loader_mode' ),
			'min'  => absint( qaf_opt( 'loader_min' ) ),
			'fx'   => (bool) qaf_opt( 'page_fx' ),
		),
		'header' => array(
			'sticky'   => (bool) qaf_opt( 'header_sticky' ),
			'autohide' => (bool) qaf_opt( 'header_autohide' ),
		),
	);
}

/**
 * Is the loader active for this request?
 *
 * @return bool
 */
function qaf_loader_enabled() {
	if ( ! qaf_opt( 'loader_on' ) || is_customize_preview() || isset( $_GET['elementor-preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return false;
	}
	return (bool) apply_filters( 'qaf_loader_enabled', true );
}

/**
 * JS flag + loader mode, before any paint. Tiny and synchronous on purpose:
 * it decides full intro vs quick curtain before the first frame, so nothing flashes.
 */
function qaf_head_flags() {
	$mode = qaf_loader_enabled() ? qaf_opt( 'loader_mode' ) : 'off';
	?>
<script data-no-optimize="1" data-no-defer="1" data-cfasync="false">(function(d){var h=d.documentElement,m=<?php echo wp_json_encode( $mode ); ?>,s=0;h.classList.add('qaf-js');try{s=sessionStorage.getItem('qafSeen')}catch(e){}if(m==='quick'||(m==='smart'&&s)){h.classList.add('qaf-loader-quick')}if(window.matchMedia&&matchMedia('(prefers-reduced-motion: reduce)').matches){h.classList.add('qaf-rm')}})(document);</script>
	<?php
}
add_action( 'wp_head', 'qaf_head_flags', 0 );

/**
 * Preload the hero image on the front page (LCP).
 */
function qaf_preload_hero() {
	if ( ! is_front_page() || ! qaf_opt( 'perf_preload' ) ) {
		return;
	}
	$image = '';
	$front = (int) get_option( 'page_on_front' );
	if ( $front && qaf_is_elementor( $front ) ) {
		$data = json_decode( (string) get_post_meta( $front, '_elementor_data', true ), true );
		$hero = is_array( $data ) ? qaf_find_widget( $data, 'qb-hero' ) : null;
		$first = $hero['settings']['slides'][0]['image'] ?? ( $hero['settings']['image'] ?? array() );
		if ( ! empty( $first['id'] ) ) {
			$image = (int) $first['id'];
		} elseif ( ! empty( $first['url'] ) ) {
			$image = $first['url'];
		}
	}
	if ( ! $image ) {
		$image = qaf_imported_media_id( 'factory-exterior.jpg' ) ? qaf_imported_media_id( 'factory-exterior.jpg' ) : 'factory-exterior.jpg';
	}
	$image = apply_filters( 'qaf_hero_preload', $image );

	if ( is_numeric( $image ) ) {
		$src    = wp_get_attachment_image_url( (int) $image, 'qaf-hero' );
		$srcset = wp_get_attachment_image_srcset( (int) $image, 'qaf-hero' );
		if ( $src ) {
			printf( '<link rel="preload" as="image" href="%s"%s imagesizes="100vw" fetchpriority="high">' . "\n", esc_url( $src ), $srcset ? ' imagesrcset="' . esc_attr( $srcset ) . '"' : '' );
		}
	} elseif ( $image ) {
		printf( '<link rel="preload" as="image" href="%s" fetchpriority="high">' . "\n", esc_url( qaf_image_url( $image ) ) );
	}
}
add_action( 'wp_head', 'qaf_preload_hero', 3 );

/**
 * GA4 / GTM from options — no raw code fields.
 */
function qaf_tracking_head() {
	$ga  = preg_replace( '/[^A-Z0-9\-]/', '', strtoupper( (string) qaf_opt( 'ga4_id' ) ) );
	$gtm = preg_replace( '/[^A-Z0-9\-]/', '', strtoupper( (string) qaf_opt( 'gtm_id' ) ) );

	if ( $gtm ) {
		printf( "<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','%s');</script>\n", esc_js( $gtm ) );
	} elseif ( $ga ) {
		printf( '<script async src="https://www.googletagmanager.com/gtag/js?id=%1$s"></script><script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments)}gtag("js",new Date());gtag("config","%1$s");</script>' . "\n", esc_attr( $ga ) );
	}
}
add_action( 'wp_head', 'qaf_tracking_head', 50 );

/**
 * Admin assets — options page, importer and post edit screens only.
 *
 * @param string $hook Screen hook.
 */
function qaf_admin_assets( $hook ) {
	$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	$is_app = $screen && in_array( $screen->post_type, array( 'qaf_application', 'qaf_entry' ), true );
	if ( ! $is_app && ! in_array( $page, array( 'qaf-options', 'qaf-import' ), true ) && ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_style( 'qaf-admin', QAF_URI . 'assets/css/admin.css', array(), qaf_asset_ver( 'assets/css/admin.css' ) );
	wp_enqueue_script( 'qaf-admin', QAF_URI . 'assets/js/admin.js', array( 'jquery', 'wp-color-picker', 'jquery-ui-sortable' ), qaf_asset_ver( 'assets/js/admin.js' ), true );
	wp_localize_script(
		'qaf-admin',
		'QAFAdmin',
		array(
			'ajax'    => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'qaf_import' ),
			'themeUri' => QAF_URI,
			'i18n'    => array(
				'pick'    => 'اختيار صورة',
				'use'     => 'استخدام هذه الصورة',
				'reset'   => 'إعادة كل الإعدادات للافتراضي؟ لا يمكن التراجع.',
				'running' => 'جارٍ التنفيذ…',
				'done'    => 'تم',
				'failed'  => 'فشل',
			),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'qaf_admin_assets' );

/**
 * Find the first widget of a type inside Elementor data.
 *
 * @param array  $elements Elements.
 * @param string $type     Widget type.
 * @return array|null
 */
function qaf_find_widget( array $elements, $type ) {
	foreach ( $elements as $el ) {
		if ( isset( $el['widgetType'] ) && $type === $el['widgetType'] ) {
			return $el;
		}
		if ( ! empty( $el['elements'] ) ) {
			$found = qaf_find_widget( $el['elements'], $type );
			if ( $found ) {
				return $found;
			}
		}
	}
	return null;
}

/**
 * Does this view render a contact / complaint form?
 *
 * @return bool
 */
function qaf_view_has_forms() {
	if ( ! is_singular( 'page' ) ) {
		return false;
	}
	$id   = get_queried_object_id();
	$data = (string) get_post_meta( $id, '_elementor_data', true );
	if ( false !== strpos( $data, '"widgetType":"qb-contact-form"' ) || false !== strpos( $data, '"widgetType":"qb-complaint-form"' ) ) {
		return true;
	}
	return in_array( get_post_meta( $id, '_qaf_import_key', true ), array( 'page-contact', 'page-complaints' ), true ) && ! qaf_is_elementor( $id );
}

/**
 * LiteSpeed / Cloudflare: never combine, defer-delay or reorder the theme scripts.
 * The loader and entrance engine are timing-sensitive; delaying them leaves content hidden.
 *
 * @param string $tag    Tag.
 * @param string $handle Handle.
 * @return string
 */
function qaf_script_no_optimize( $tag, $handle ) {
	if ( 0 === strpos( $handle, 'qaf-' ) && false === strpos( $tag, 'data-no-optimize' ) ) {
		$tag = str_replace( '<script ', '<script data-no-optimize="1" data-no-defer="1" data-cfasync="false" ', $tag );
	}
	return $tag;
}
add_filter( 'script_loader_tag', 'qaf_script_no_optimize', 10, 2 );

/**
 * Same for the inline config printed with the theme scripts (QAFCfg).
 *
 * @param array $attributes Attributes.
 * @return array
 */
function qaf_inline_no_optimize( $attributes ) {
	if ( isset( $attributes['id'] ) && 0 === strpos( (string) $attributes['id'], 'qaf-' ) ) {
		$attributes['data-no-optimize'] = '1';
		$attributes['data-no-defer']    = '1';
		$attributes['data-cfasync']     = 'false';
	}
	return $attributes;
}
add_filter( 'wp_inline_script_attributes', 'qaf_inline_no_optimize' );

/**
 * Tell LiteSpeed to exclude theme JS from "Delay JS" / "Load JS Deferred" explicitly.
 *
 * @param array $list Excludes.
 * @return array
 */
function qaf_litespeed_js_excludes( $list ) {
	$list   = (array) $list;
	$list[] = 'qafesheh-theme/assets/js/';
	$list[] = 'QAFCfg';
	return $list;
}
add_filter( 'litespeed_optimize_js_excludes', 'qaf_litespeed_js_excludes' );
add_filter( 'litespeed_optm_js_defer_exc', 'qaf_litespeed_js_excludes' );
add_filter( 'litespeed_optm_gm_js_exc', 'qaf_litespeed_js_excludes' );

/**
 * Speculation Rules: the browser quietly prerenders the next page on hover, so moving around the
 * site feels instant. Admin and logged-in sessions excluded.
 */
function qaf_speculation_rules() {
	if ( ! qaf_opt( 'perf_speculate' ) || is_admin() || is_user_logged_in() ) {
		return;
	}
	$rules = array(
		'prerender' => array(
			array(
				'source'    => 'document',
				'where'     => array(
					'and' => array(
						array( 'href_matches' => '/*' ),
						array( 'not' => array( 'href_matches' => array( '/wp-admin/*', '/wp-login.php', '/*\\?*apply=*' ) ) ),
						array( 'not' => array( 'selector_matches' => '[data-qaf-nofx], [download], [target="_blank"]' ) ),
					),
				),
				'eagerness' => 'moderate',
			),
		),
	);
	echo '<script type="speculationrules">' . wp_json_encode( $rules ) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput
}
add_action( 'wp_footer', 'qaf_speculation_rules', 99 );

/**
 * Skip rendering sections that are far off-screen (paint less on first load).
 */
function qaf_content_visibility() {
	if ( ! qaf_opt( 'perf_cv' ) || is_admin() ) {
		return;
	}
	wp_add_inline_style( 'qaf-base', '@media screen{.qaf-sec{content-visibility:auto;contain-intrinsic-size:auto 640px}.qaf-sec:first-of-type,.qaf-hero,.qaf-banner{content-visibility:visible}}@media print{.qaf-sec{content-visibility:visible}}' );
}
add_action( 'wp_enqueue_scripts', 'qaf_content_visibility', 30 );
