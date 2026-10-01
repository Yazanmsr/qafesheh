<?php
/**
 * QB Values.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Repeater;

/**
 * Hairline grid of values with icons.
 */
class QAF_Widget_Values extends QAF_Widget_Base {

	/** @return string */
	public function get_name() {
		return 'qb-values';
	}

	/** @return string */
	public function get_title() {
		return 'QB Values';
	}

	/** @return string */
	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	/** Controls. */
	protected function register_controls() {
		$seed = qaf_seed_page_copy( 'about' )['values'];
		$this->start_controls_section( 'content', array( 'label' => 'المحتوى' ) );
		$this->qaf_text( 'kicker', 'السطر العلوي', $seed['kicker'] );
		$this->qaf_text( 'title', 'العنوان', $seed['title'] );
		$rep = new Repeater();
		$rep->add_control( 'icon', array( 'label' => 'الأيقونة', 'type' => Controls_Manager::SELECT, 'options' => qaf_icon_choices(), 'default' => 'star' ) );
		$rep->add_control( 'title', array( 'label' => 'العنوان', 'type' => Controls_Manager::TEXT, 'label_block' => true ) );
		$rep->add_control( 'text', array( 'label' => 'النص', 'type' => Controls_Manager::TEXTAREA ) );
		$this->add_control( 'items', array( 'label' => 'القيم', 'type' => Controls_Manager::REPEATER, 'fields' => $rep->get_controls(), 'default' => $seed['items'], 'title_field' => '{{{ title }}}' ) );
		$this->end_controls_section();
		$this->qaf_style_section( array( 'title' => array( 'العنوان', '.qaf-sechead__title' ), 'item' => array( 'عناوين القيم', '.qaf-values__title' ) ) );
		$this->qaf_add_anim_section();
	}

	/** Render. */
	protected function render() {
		$s = $this->get_settings_for_display();
		qaf_section( 'values', array( 'kicker' => $s['kicker'], 'title' => $s['title'], 'items' => (array) $s['items'] ) + $this->qaf_base_args( $s ) );
	}
}
