<?php
/**
 * Self-hosted fonts (singleton).
 * One source only: local WOFF2 in assets/fonts. Elementor Google Fonts disabled.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

/**
 * QAF_Fonts.
 */
final class QAF_Fonts {

	/**
	 * Instance.
	 *
	 * @var QAF_Fonts|null
	 */
	private static $instance = null;

	/**
	 * Get instance.
	 *
	 * @return QAF_Fonts
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Hooks.
	 */
	private function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'register' ), 1 );
		add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'enqueue_editor' ) );
		add_action( 'elementor/preview/enqueue_styles', array( $this, 'enqueue_editor' ) );
		add_action( 'wp_head', array( $this, 'preload' ), 2 );
		add_filter( 'elementor/fonts/groups', array( $this, 'elementor_group' ) );
		add_filter( 'elementor/fonts/additional_fonts', array( $this, 'elementor_fonts' ) );
		add_filter( 'elementor/frontend/print_google_fonts', '__return_false' );
	}

	/**
	 * Families the theme ships.
	 *
	 * @return string[]
	 */
	public function families() {
		return array( 'Noto Naskh Arabic', 'IBM Plex Sans Arabic', 'Libre Baskerville' );
	}

	/**
	 * Critical faces (≤4).
	 *
	 * @return string[]
	 */
	public function critical() {
		$faces = array(
			'assets/fonts/ibm-plex-sans-arabic/ibm-plex-sans-arabic-arabic-300-normal.woff2',
			'assets/fonts/libre-baskerville/libre-baskerville-latin-400-normal.woff2',
		);
		if ( is_rtl() ) {
			array_unshift( $faces, 'assets/fonts/noto-naskh-arabic/noto-naskh-arabic-arabic-700-normal.woff2' );
		}
		return $faces;
	}

	/**
	 * Register stylesheet.
	 */
	public function register() {
		wp_register_style( 'qaf-fonts', QAF_URI . 'assets/css/qaf-fonts.css', array(), qaf_asset_ver( 'assets/css/qaf-fonts.css' ) );
	}

	/**
	 * Editor.
	 */
	public function enqueue_editor() {
		wp_enqueue_style( 'qaf-fonts', QAF_URI . 'assets/css/qaf-fonts.css', array(), qaf_asset_ver( 'assets/css/qaf-fonts.css' ) );
	}

	/**
	 * Preload critical faces.
	 */
	public function preload() {
		if ( ! qaf_opt( 'perf_preload' ) ) {
			return;
		}
		foreach ( $this->critical() as $rel ) {
			printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url( QAF_URI . $rel ) );
		}
	}

	/**
	 * Elementor font group.
	 *
	 * @param array $groups Groups.
	 * @return array
	 */
	public function elementor_group( $groups ) {
		return array( 'qaf' => 'QB — Qafesheh' ) + $groups;
	}

	/**
	 * Elementor fonts.
	 *
	 * @param array $fonts Fonts.
	 * @return array
	 */
	public function elementor_fonts( $fonts ) {
		foreach ( $this->families() as $family ) {
			$fonts[ $family ] = 'qaf';
		}
		return $fonts;
	}
}

QAF_Fonts::instance();
