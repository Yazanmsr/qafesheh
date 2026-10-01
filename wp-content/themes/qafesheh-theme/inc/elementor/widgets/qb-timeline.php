<?php
/**
 * QB Timeline.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Repeater;

/**
 * Milestones on a dashed track that fills as it enters.
 */
class QAF_Widget_Timeline extends QAF_Widget_Base {

	/** @return string */
	public function get_name() {
		return 'qb-timeline';
	}

	/** @return string */
	public function get_title() {
		return 'QB Timeline';
	}

	/** @return string */
	public function get_icon() {
		return 'eicon-time-line';
	}

	/** Controls. */
	protected function register_controls() {
		$seed = qaf_seed_page_copy( 'about' )['timeline'];
		$this->start_controls_section( 'content', array( 'label' => 'المحتوى' ) );
		$this->qaf_text( 'kicker', 'السطر العلوي', $seed['kicker'] );
		$this->qaf_text( 'title', 'العنوان', $seed['title'] );
		$rep = new Repeater();
		$rep->add_control( 'year', array( 'label' => 'السنة / المرحلة', 'type' => Controls_Manager::TEXT ) );
		$rep->add_control( 'title', array( 'label' => 'العنوان', 'type' => Controls_Manager::TEXT, 'label_block' => true ) );
		$rep->add_control( 'text', array( 'label' => 'الوصف', 'type' => Controls_Manager::TEXTAREA ) );
		$this->add_control( 'items', array( 'label' => 'المحطات', 'type' => Controls_Manager::REPEATER, 'fields' => $rep->get_controls(), 'default' => $seed['items'], 'title_field' => '{{{ year }}} — {{{ title }}}' ) );
		$this->end_controls_section();
		$this->qaf_style_section( array( 'title' => array( 'العنوان', '.qaf-sechead__title' ), 'year' => array( 'السنوات', '.qaf-timeline__year' ) ) );
		$this->qaf_add_anim_section();
	}

	/** Render. */
	protected function render() {
		$s = $this->get_settings_for_display();
		qaf_section( 'timeline', array( 'kicker' => $s['kicker'], 'title' => $s['title'], 'items' => (array) $s['items'] ) + $this->qaf_base_args( $s ) );
	}
}
