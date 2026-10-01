<?php
/**
 * Theme supports, menus, image sizes, body classes, favicon, head cleanup.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register supports, menus and image sizes.
 */
function qaf_setup() {
	load_theme_textdomain( 'qafesheh', QAF_DIR . 'languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );

	register_nav_menus(
		array(
			'primary'        => __( 'Primary menu', 'qafesheh' ),
			'utility'        => __( 'Top bar links', 'qafesheh' ),
			'footer_group'   => __( 'Footer — Group', 'qafesheh' ),
			'footer_brands'  => __( 'Footer — Our companies', 'qafesheh' ),
			'footer_service' => __( 'Footer — Customer service', 'qafesheh' ),
			'footer_legal'   => __( 'Footer — Legal', 'qafesheh' ),
		)
	);

	add_image_size( 'qaf-hero', 2560, 1350, true );
	add_image_size( 'qaf-banner', 1920, 760, true );
	add_image_size( 'qaf-card', 900, 900, true );
	add_image_size( 'qaf-wide', 1400, 1000, true );
	add_image_size( 'qaf-thumb', 640, 440, true );
}
add_action( 'after_setup_theme', 'qaf_setup' );

/**
 * Body classes that drive the design system.
 *
 * @param string[] $classes Classes.
 * @return string[]
 */
function qaf_body_class( $classes ) {
	$classes[] = 'qaf-theme';
	$classes[] = 'qaf-lang-' . sanitize_html_class( qaf_current_lang() );

	if ( is_rtl() ) {
		$classes[] = 'qaf-rtl';
	}
	if ( qaf_opt( 'anim_on' ) ) {
		$classes[] = 'qaf-anim-on';
	}
	if ( qaf_opt( 'header_sticky' ) ) {
		$classes[] = 'qaf-header-sticky';
	}
	if ( qaf_loader_enabled() ) {
		$classes[] = 'qaf-loader-on';
	}

	return $classes;
}
add_filter( 'body_class', 'qaf_body_class' );

/**
 * Favicon from options when no Site Icon is set.
 */
function qaf_favicon() {
	if ( has_site_icon() ) {
		return;
	}
	$icon = qaf_opt( 'favicon' );
	if ( $icon ) {
		printf( '<link rel="icon" href="%s">' . "\n", esc_url( $icon ) );
	}
}
add_action( 'wp_head', 'qaf_favicon', 4 );

/**
 * Theme color for mobile browser chrome.
 */
function qaf_theme_color() {
	printf( '<meta name="theme-color" content="%s">' . "\n", esc_attr( qaf_opt( 'c_dark' ) ) );
}
add_action( 'wp_head', 'qaf_theme_color', 1 );

/**
 * Head cleanup + switchable performance cleanups.
 */
function qaf_perf_cleanup() {
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );

	if ( qaf_opt( 'perf_emoji' ) ) {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		add_filter( 'emoji_svg_url', '__return_false' );
	}
	if ( qaf_opt( 'perf_embed' ) ) {
		remove_action( 'wp_head', 'wp_oembed_add_host_js' );
	}
}
add_action( 'init', 'qaf_perf_cleanup' );

/**
 * Front-end dequeues driven by performance options.
 */
function qaf_perf_dequeue() {
	if ( qaf_opt( 'perf_embed' ) ) {
		wp_deregister_script( 'wp-embed' );
	}
	if ( qaf_opt( 'perf_dashicons' ) && ! is_user_logged_in() ) {
		wp_dequeue_style( 'dashicons' );
	}
	if ( qaf_opt( 'perf_block_css' ) && ! is_singular( 'post' ) ) {
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'wp-block-library-theme' );
		wp_dequeue_style( 'global-styles' );
		wp_dequeue_style( 'classic-theme-styles' );
	}
}
add_action( 'wp_enqueue_scripts', 'qaf_perf_dequeue', 100 );

/**
 * Drop jquery-migrate on the front end.
 *
 * @param WP_Scripts $scripts Scripts registry.
 */
function qaf_no_jquery_migrate( $scripts ) {
	if ( is_admin() || ! qaf_opt( 'perf_migrate' ) || empty( $scripts->registered['jquery'] ) ) {
		return;
	}
	$scripts->registered['jquery']->deps = array_diff( $scripts->registered['jquery']->deps, array( 'jquery-migrate' ) );
}
add_action( 'wp_default_scripts', 'qaf_no_jquery_migrate' );

/**
 * Excerpt length for cards.
 *
 * @return int
 */
function qaf_excerpt_length() {
	return 22;
}
add_filter( 'excerpt_length', 'qaf_excerpt_length' );

/**
 * Plain excerpt suffix.
 *
 * @return string
 */
function qaf_excerpt_more() {
	return '…';
}
add_filter( 'excerpt_more', 'qaf_excerpt_more' );

/**
 * Menu highlighting for content types: WordPress marks the blog page as "current parent"
 * on every CPT view — move that state to the matching archive item instead.
 *
 * @param string[] $classes Classes.
 * @param WP_Post  $item    Menu item.
 * @return string[]
 */
function qaf_nav_current_classes( $classes, $item ) {
	if ( is_singular( 'post' ) || is_home() || is_category() || is_tag() ) {
		return $classes;
	}
	$type = is_singular() ? get_post_type() : ( is_post_type_archive() ? (string) get_query_var( 'post_type' ) : '' );
	if ( ! $type || 'page' === $type ) {
		return $classes;
	}
	$blog = (int) get_option( 'page_for_posts' );
	if ( $blog && (int) $item->object_id === (int) apply_filters( 'wpml_object_id', $blog, 'page', true ) ) {
		$classes = array_diff( $classes, array( 'current_page_parent', 'current-menu-parent' ) );
	}
	$archive = get_post_type_archive_link( $type );
	$is_item = ( 'post_type_archive' === $item->type && $type === $item->object ) || ( $archive && 'custom' === $item->type && untrailingslashit( $item->url ) === untrailingslashit( (string) apply_filters( 'wpml_permalink', $archive ) ) );
	if ( $is_item ) {
		$classes[] = is_singular() ? 'current-menu-ancestor' : 'current-menu-item';
	}
	return array_unique( $classes );
}
add_filter( 'nav_menu_css_class', 'qaf_nav_current_classes', 10, 2 );
