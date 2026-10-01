<?php
/**
 * QB Vision & Mission.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;


/**
 * Two dark columns: vision and mission.
 */
class QAF_Widget_Vision extends QAF_Widget_Base {

	/** @return string */
	public function get_name() {
		return 'qb-vision';
	}

	/** @return string */
	public function get_title() {
		return 'QB Vision & Mission';
	}

	/** @return string */
	public function get_icon() {
		return 'eicon-columns';
	}

	/** Controls. */
	protected function register_controls() {
		$seed = qaf_seed_page_copy( 'about' )['vision'];
		$this->start_controls_section( 'content', array( 'label' => 'المحتوى' ) );
		$this->qaf_text( 'v_title', 'العنوان الأول', $seed['v_title'] );
		$this->qaf_text( 'v_text', 'النص الأول', $seed['v_text'], 'TEXTAREA' );
		$this->qaf_text( 'm_title', 'العنوان الثاني', $seed['m_title'] );
		$this->qaf_text( 'm_text', 'النص الثاني', $seed['m_text'], 'TEXTAREA' );
		$this->end_controls_section();
		$this->qaf_style_section( array( 'title' => array( 'العناوين', '.qaf-vision__title' ), 'text' => array( 'النصوص', '.qaf-vision__text' ) ) );
		$this->qaf_add_anim_section();
	}

	/** Render. */
	protected function render() {
		$s = $this->get_settings_for_display();
		qaf_section( 'vision', array( 'v_title' => $s['v_title'], 'v_text' => $s['v_text'], 'm_title' => $s['m_title'], 'm_text' => $s['m_text'] ) + $this->qaf_base_args( $s ) );
	}
}
