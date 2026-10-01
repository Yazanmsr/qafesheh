<?php
/**
 * QB Steps Bar.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Repeater;

/**
 * Numbered “how it works” bar.
 */
class QAF_Widget_Steps_Bar extends QAF_Widget_Base {

	/** @return string */
	public function get_name() {
		return 'qb-steps-bar';
	}

	/** @return string */
	public function get_title() {
		return 'QB Steps Bar';
	}

	/** @return string */
	public function get_icon() {
		return 'eicon-number-field';
	}

	/** Controls. */
	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => 'المحتوى' ) );
		$rep = new Repeater();
		$rep->add_control( 'title', array( 'label' => 'العنوان', 'type' => Controls_Manager::TEXT, 'label_block' => true ) );
		$rep->add_control( 'text', array( 'label' => 'النص', 'type' => Controls_Manager::TEXTAREA ) );
		$this->add_control( 'items', array( 'label' => 'الخطوات', 'type' => Controls_Manager::REPEATER, 'fields' => $rep->get_controls(), 'default' => qaf_seed_page_copy( 'complaints' )['steps'], 'title_field' => '{{{ title }}}' ) );
		$this->end_controls_section();
		$this->qaf_style_section( array( 'title' => array( 'العناوين', '.qaf-stepsbar__title' ) ) );
		$this->qaf_add_anim_section();
	}

	/** Render. */
	protected function render() {
		$s = $this->get_settings_for_display();
		qaf_section( 'steps-bar', array( 'items' => (array) $s['items'] ) + $this->qaf_base_args( $s ) );
	}
}
