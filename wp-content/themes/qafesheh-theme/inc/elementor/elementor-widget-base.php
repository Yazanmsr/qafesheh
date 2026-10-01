<?php
/**
 * Base class for QB widgets.
 * Widgets only map controls → args; markup lives in template-parts/home, shared with front-page.php.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

/**
 * QAF_Widget_Base.
 */
abstract class QAF_Widget_Base extends Widget_Base {

	/** @return string[] */
	public function get_categories() {
		return array( 'qb-qaf' );
	}

	/** @return string[] */
	public function get_keywords() {
		return array( 'qb', 'qafesheh', 'section' );
	}

	/** @return string[] */
	public function get_style_depends() {
		return array( 'qaf-base', 'qaf-widgets' );
	}

	/** @return string[] */
	public function get_script_depends() {
		return array( 'qaf-ui' );
	}

	/**
	 * Seed defaults for this section in the site's default language.
	 *
	 * @param string $section Section key.
	 * @return array
	 */
	protected function seed( $section ) {
		return qaf_seed_home( qaf_current_lang() )[ $section ] ?? array();
	}

	/**
	 * Chapter number + label controls.
	 *
	 * @param array $seed Seed.
	 */
	protected function qaf_chapter_controls( $seed ) {
		$this->add_control(
			'num',
			array(
				'label'   => 'رقم الفصل',
				'type'    => Controls_Manager::TEXT,
				'default' => $seed['num'] ?? '',
				'dynamic' => array( 'active' => true ),
			)
		);
		$this->add_control(
			'label',
			array(
				'label'       => 'اسم الفصل',
				'type'        => Controls_Manager::TEXT,
				'default'     => $seed['label'] ?? '',
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
			)
		);
	}

	/**
	 * Text control shortcut.
	 *
	 * @param string $key     Key.
	 * @param string $label   Label.
	 * @param string $default Default.
	 * @param string $type    TEXT|TEXTAREA.
	 */
	protected function qaf_text( $key, $label, $default = '', $type = 'TEXT' ) {
		$this->add_control(
			$key,
			array(
				'label'       => $label,
				'type'        => 'TEXTAREA' === $type ? Controls_Manager::TEXTAREA : Controls_Manager::TEXT,
				'default'     => $default,
				'label_block' => true,
				'rows'        => 3,
				'dynamic'     => array( 'active' => true ),
			)
		);
	}

	/**
	 * URL control (stored as URL array).
	 *
	 * @param string $key     Key.
	 * @param string $label   Label.
	 * @param string $default Default URL.
	 */
	protected function qaf_url( $key, $label, $default = '' ) {
		$this->add_control(
			$key,
			array(
				'label'   => $label,
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => $default ),
				'dynamic' => array( 'active' => true ),
			)
		);
	}

	/**
	 * Media control with a bundled default image.
	 *
	 * @param string $key   Key.
	 * @param string $label Label.
	 * @param string $file  Bundled file name.
	 */
	protected function qaf_media( $key, $label, $file ) {
		$this->add_control(
			$key,
			array(
				'label'   => $label,
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => qaf_image_url( $file ),
					'id'  => qaf_imported_media_id( $file ),
				),
				'dynamic' => array( 'active' => true ),
			)
		);
	}

	/**
	 * Style section: title/text colours + typography for common selectors.
	 *
	 * @param array $map [ key => [ label, selector ] ].
	 */
	protected function qaf_style_section( $map ) {
		$this->start_controls_section( 'qaf_style', array( 'label' => 'التنسيق', 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_control(
			'section_bg',
			array(
				'label'     => 'خلفية القسم',
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} section, {{WRAPPER}} .qaf-sec' => 'background-color: {{VALUE}};' ),
			)
		);
		foreach ( $map as $key => $def ) {
			$this->add_control(
				$key . '_color',
				array(
					'label'     => $def[0] . ' — اللون',
					'type'      => Controls_Manager::COLOR,
					'selectors' => array( '{{WRAPPER}} ' . $def[1] => 'color: {{VALUE}};' ),
				)
			);
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => $key . '_typo',
					'label'    => $def[0],
					'selector' => '{{WRAPPER}} ' . $def[1],
				)
			);
		}
		$this->end_controls_section();
	}

	/**
	 * Shared “Section options” (anchor id). Entrance animation is built into every section.
	 */
	protected function qaf_add_anim_section() {
		$this->start_controls_section( 'qaf_advanced', array( 'label' => 'إعدادات القسم', 'tab' => Controls_Manager::TAB_ADVANCED ) );
		$this->add_control(
			'anchor',
			array(
				'label'       => 'معرّف الرابط (Anchor)',
				'type'        => Controls_Manager::TEXT,
				'description' => 'للربط المباشر مثل #companies.',
			)
		);
		$this->add_control(
			'anim_note',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => 'حركات الدخول مدمجة بتصميم القسم، وتُضبط مدتها من «خيارات الثيم ← الحركة».',
			)
		);
		$this->end_controls_section();
	}

	/**
	 * URL control value → string.
	 *
	 * @param mixed $link URL control.
	 * @return string
	 */
	protected function qaf_link_value( $link ) {
		return is_array( $link ) ? (string) ( $link['url'] ?? '' ) : (string) $link;
	}

	/**
	 * Media control value → id or url.
	 *
	 * @param mixed $media Media control.
	 * @return int|string
	 */
	protected function qaf_media_value( $media ) {
		if ( ! is_array( $media ) ) {
			return '';
		}
		return ! empty( $media['id'] ) ? (int) $media['id'] : (string) ( $media['url'] ?? '' );
	}

	/**
	 * Common args from settings.
	 *
	 * @param array $s Settings.
	 * @return array
	 */
	protected function qaf_base_args( $s ) {
		return array( 'id' => sanitize_html_class( (string) ( $s['anchor'] ?? '' ) ) );
	}
}
