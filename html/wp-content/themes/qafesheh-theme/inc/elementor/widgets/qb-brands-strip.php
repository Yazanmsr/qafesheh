<?php
/**
 * QB Brands Strip.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;

/**
 * The companies as a dark strip (reads the Companies content type).
 */
class QAF_Widget_Brands_Strip extends QAF_Widget_Base {

	/** @return string */
	public function get_name() {
		return 'qb-brands-strip';
	}

	/** @return string */
	public function get_title() {
		return 'QB Brands Strip';
	}

	/** @return string */
	public function get_icon() {
		return 'eicon-logo';
	}

	/** Controls. */
	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => 'المحتوى' ) );
		$this->add_control( 'note', array( 'type' => Controls_Manager::RAW_HTML, 'raw' => 'يقرأ الشعار والاسم واللون والتخصص من «الشركات» في لوحة التحكم.' ) );
		$this->end_controls_section();
		$this->qaf_add_anim_section();
	}

	/** Render. */
	protected function render() {
		$s = $this->get_settings_for_display();
		qaf_section( 'brands-strip', $this->qaf_base_args( $s ) );
	}
}
