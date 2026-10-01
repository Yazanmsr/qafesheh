<?php
/**
 * Elementor integration: category, theme locations, widget registry, asset registration.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

/**
 * Widget registry: slug => class (file = widgets/{slug}.php).
 *
 * @return array
 */
function qaf_widget_registry() {
	return array(
		'qb-hero'         => 'QAF_Widget_Hero',
		'qb-statement'    => 'QAF_Widget_Statement',
		'qb-companies'    => 'QAF_Widget_Companies',
		'qb-offers'       => 'QAF_Widget_Offers',
		'qb-capabilities' => 'QAF_Widget_Capabilities',
		'qb-services'     => 'QAF_Widget_Services',
		'qb-standards'    => 'QAF_Widget_Standards',
		'qb-posts'        => 'QAF_Widget_Posts',
		'qb-branches'     => 'QAF_Widget_Branches',
		'qb-cta'          => 'QAF_Widget_CTA',
		'qb-page-hero'    => 'QAF_Widget_Page_Hero',
		'qb-story'        => 'QAF_Widget_Story',
		'qb-timeline'     => 'QAF_Widget_Timeline',
		'qb-vision'       => 'QAF_Widget_Vision',
		'qb-values'       => 'QAF_Widget_Values',
		'qb-brands-strip' => 'QAF_Widget_Brands_Strip',
		'qb-channels'     => 'QAF_Widget_Channels',
		'qb-steps-bar'    => 'QAF_Widget_Steps_Bar',
		'qb-contact-form' => 'QAF_Widget_Contact_Form',
		'qb-complaint-form' => 'QAF_Widget_Complaint_Form',
	);
}

/**
 * Category.
 *
 * @param \Elementor\Elements_Manager $manager Manager.
 */
function qaf_elementor_category( $manager ) {
	$manager->add_category(
		'qb-qaf',
		array(
			'title' => 'QB — Qafesheh Widgets',
			'icon'  => 'eicon-star',
		)
	);
}
add_action( 'elementor/elements/categories_registered', 'qaf_elementor_category' );

/**
 * Register widgets.
 *
 * @param \Elementor\Widgets_Manager $manager Manager.
 */
function qaf_elementor_widgets( $manager ) {
	require_once QAF_DIR . 'inc/elementor/elementor-widget-base.php';
	foreach ( qaf_widget_registry() as $slug => $class ) {
		$file = QAF_DIR . 'inc/elementor/widgets/' . $slug . '.php';
		if ( file_exists( $file ) ) {
			require_once $file;
			if ( class_exists( $class ) ) {
				$manager->register( new $class() );
			}
		}
	}
}
add_action( 'elementor/widgets/register', 'qaf_elementor_widgets' );

/**
 * Theme Builder can override header/footer.
 *
 * @param \ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $manager Manager.
 */
function qaf_elementor_locations( $manager ) {
	$manager->register_all_core_location();
}
add_action( 'elementor/theme/register_locations', 'qaf_elementor_locations' );

/**
 * Elementor may render before wp_enqueue_scripts — register our handles early.
 */
function qaf_elementor_register_assets() {
	if ( ! wp_style_is( 'qaf-fonts', 'registered' ) ) {
		QAF_Fonts::instance()->register();
	}
	if ( ! wp_style_is( 'qaf-base', 'registered' ) ) {
		qaf_register_assets();
	}
}
add_action( 'elementor/frontend/after_register_styles', 'qaf_elementor_register_assets', 1 );
add_action( 'elementor/frontend/after_register_scripts', 'qaf_elementor_register_assets', 1 );

/**
 * Editor preview gets tokens, base and widget styles.
 */
function qaf_elementor_preview_styles() {
	qaf_elementor_register_assets();
	wp_enqueue_style( 'qaf-base' );
	wp_enqueue_style( 'qaf-widgets' );
	wp_enqueue_style( 'qaf-forms' );
	if ( is_rtl() ) {
		wp_enqueue_style( 'qaf-rtl' );
	}
}
add_action( 'elementor/preview/enqueue_styles', 'qaf_elementor_preview_styles' );

/**
 * Sensible Elementor defaults for this theme (no Elementor global colors/fonts overriding tokens).
 */
function qaf_elementor_defaults() {
	if ( get_option( 'qaf_elementor_defaults' ) ) {
		return;
	}
	update_option( 'elementor_disable_color_schemes', 'yes' );
	update_option( 'elementor_disable_typography_schemes', 'yes' );
	update_option( 'elementor_font_display', 'swap' );
	update_option( 'qaf_elementor_defaults', 1, false );
}
add_action( 'elementor/init', 'qaf_elementor_defaults' );
