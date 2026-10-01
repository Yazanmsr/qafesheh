<?php
/**
 * QB CTA.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

/**
 * Dark closing band with two actions.
 */
class QAF_Widget_CTA extends QAF_Widget_Base {

	/** @return string */
	public function get_name() {
		return 'qb-cta';
	}

	/** @return string */
	public function get_title() {
		return 'QB CTA';
	}

	/** @return string */
	public function get_icon() {
		return 'eicon-call-to-action';
	}

	/** Controls. */
	protected function register_controls() {
		$seed = $this->seed( 'cta' );
		$this->start_controls_section( 'content', array( 'label' => 'المحتوى' ) );
		$this->qaf_text( 'title', 'العنوان', $seed['title'] ?? '', 'TEXTAREA' );
		$this->qaf_text( 'text', 'النص', $seed['text'] ?? '', 'TEXTAREA' );
		$this->qaf_text( 'btn1', 'الزر الأول', $seed['btn1'] ?? '' );
		$this->qaf_url( 'btn1_url', 'رابط الزر الأول', $seed['btn1_url'] ?? '' );
		$this->qaf_text( 'btn2', 'الزر الثاني', $seed['btn2'] ?? '' );
		$this->qaf_url( 'btn2_url', 'رابط الزر الثاني', $seed['btn2_url'] ?? '' );
		$this->end_controls_section();

		$this->qaf_style_section(
			array(
				'title' => array( 'العنوان', '.qaf-cta__title' ),
				'text'  => array( 'النص', '.qaf-cta__text' ),
			)
		);
		$this->qaf_add_anim_section();
	}

	/** Render. */
	protected function render() {
		$s = $this->get_settings_for_display();
		qaf_section(
			'cta',
			array(
				'title'    => $s['title'],
				'text'     => $s['text'],
				'btn1'     => $s['btn1'],
				'btn1_url' => $this->qaf_link_value( $s['btn1_url'] ),
				'btn2'     => $s['btn2'],
				'btn2_url' => $this->qaf_link_value( $s['btn2_url'] ),
			) + $this->qaf_base_args( $s )
		);
	}
}
