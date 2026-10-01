<?php
/**
 * QB Standards.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Repeater;

/**
 * Dark title/text rows.
 */
class QAF_Widget_Standards extends QAF_Widget_Base {

	/** @return string */
	public function get_name() {
		return 'qb-standards';
	}

	/** @return string */
	public function get_title() {
		return 'QB Standards';
	}

	/** @return string */
	public function get_icon() {
		return 'eicon-bullet-list';
	}

	/** Controls. */
	protected function register_controls() {
		$seed = $this->seed( 'standards' );
		$this->start_controls_section( 'content', array( 'label' => 'المحتوى' ) );
		$this->qaf_chapter_controls( $seed );
		$rep = new Repeater();
		$rep->add_control( 'title', array( 'label' => 'العنوان', 'type' => Controls_Manager::TEXT, 'label_block' => true ) );
		$rep->add_control( 'text', array( 'label' => 'النص', 'type' => Controls_Manager::TEXTAREA ) );
		$this->add_control(
			'rows',
			array(
				'label'       => 'البنود',
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $rep->get_controls(),
				'default'     => $seed['rows'] ?? array(),
				'title_field' => '{{{ title }}}',
			)
		);
		$this->end_controls_section();

		$this->qaf_style_section(
			array(
				'title' => array( 'العناوين', '.qaf-rows__title' ),
				'text'  => array( 'النصوص', '.qaf-rows__text' ),
			)
		);
		$this->qaf_add_anim_section();
	}

	/** Render. */
	protected function render() {
		$s = $this->get_settings_for_display();
		qaf_section(
			'standards',
			array(
				'num'   => $s['num'],
				'label' => $s['label'],
				'rows'  => (array) $s['rows'],
			) + $this->qaf_base_args( $s )
		);
	}
}
