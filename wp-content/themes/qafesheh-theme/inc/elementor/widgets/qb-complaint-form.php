<?php
/**
 * QB Complaint Form.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;

/**
 * Complaints form with tracking number, pledge, direct contact and status lookup.
 */
class QAF_Widget_Complaint_Form extends QAF_Widget_Base {

	/** @return string */
	public function get_name() {
		return 'qb-complaint-form';
	}

	/** @return string */
	public function get_title() {
		return 'QB Complaint Form';
	}

	/** @return string */
	public function get_icon() {
		return 'eicon-form-vertical';
	}

	/** @return string[] */
	public function get_style_depends() {
		return array( 'qaf-base', 'qaf-widgets', 'qaf-forms' );
	}

	/** @return string[] */
	public function get_script_depends() {
		return array( 'qaf-ui', 'qaf-forms' );
	}

	/** Controls. */
	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => 'المحتوى' ) );
		$this->qaf_text( 'title', 'عنوان النموذج', qaf_seed_page_copy( 'complaints' )['form']['title'] );
		foreach ( array( 'pledge' => 'بطاقة التعهّد', 'direct' => 'التواصل المباشر', 'track' => 'متابعة رسالة سابقة' ) as $k => $l ) {
			$this->add_control( $k, array( 'label' => $l, 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'return_value' => 'yes' ) );
		}
		$this->add_control( 'note', array( 'type' => Controls_Manager::RAW_HTML, 'raw' => 'الإيميلات والنصوص من «خيارات الثيم ← النماذج والرسائل». الرسائل تُخزَّن في «الرسائل» برقم متابعة.' ) );
		$this->end_controls_section();
		$this->qaf_add_anim_section();
	}

	/** Render. */
	protected function render() {
		$s = $this->get_settings_for_display();
		qaf_section( 'complaint-form', array( 'title' => $s['title'], 'pledge' => 'yes' === $s['pledge'], 'direct' => 'yes' === $s['direct'], 'track' => 'yes' === $s['track'] ) + $this->qaf_base_args( $s ) );
	}
}
