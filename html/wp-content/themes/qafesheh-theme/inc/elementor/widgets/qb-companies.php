<?php
/**
 * QB Companies.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;

/**
 * Brand cards from the Companies content type.
 */
class QAF_Widget_Companies extends QAF_Widget_Base {

	/** @return string */
	public function get_name() {
		return 'qb-companies';
	}

	/** @return string */
	public function get_title() {
		return 'QB Companies';
	}

	/** @return string */
	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	/** Controls. */
	protected function register_controls() {
		$seed = $this->seed( 'companies' );
		$this->start_controls_section( 'content', array( 'label' => 'المحتوى' ) );
		$this->qaf_chapter_controls( $seed );
		$this->qaf_text( 'title', 'العنوان', $seed['title'] ?? '' );
		$this->qaf_text( 'text', 'المقدمة', $seed['text'] ?? '', 'TEXTAREA' );
		$this->add_control( 'count', array( 'label' => 'عدد الشركات', 'type' => Controls_Manager::NUMBER, 'min' => 1, 'max' => 12, 'default' => 3 ) );
		$this->add_control(
			'note',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => 'البطاقات تُقرأ من «الشركات» في لوحة التحكم: الصورة البارزة، اللون، الشعار، التخصص، الأسواق ونقاط البيع. الترتيب من حقل «الترتيب».',
			)
		);
		$this->end_controls_section();

		$this->qaf_style_section(
			array(
				'title' => array( 'العنوان', '.qaf-h2' ),
				'card'  => array( 'اسم العلامة', '.qaf-brand__title' ),
			)
		);
		$this->qaf_add_anim_section();
		$this->update_control( 'anchor', array( 'default' => 'companies' ) );
	}

	/** Render. */
	protected function render() {
		$s = $this->get_settings_for_display();
		qaf_section(
			'companies',
			array(
				'num'   => $s['num'],
				'label' => $s['label'],
				'title' => $s['title'],
				'text'  => $s['text'],
				'count' => absint( $s['count'] ),
			) + $this->qaf_base_args( $s )
		);
	}
}
