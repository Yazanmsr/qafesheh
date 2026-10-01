<?php
/**
 * QB Contact Form.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;

/**
 * Contact form with brands card, complaints promo and map.
 */
class QAF_Widget_Contact_Form extends QAF_Widget_Base {

	/** @return string */
	public function get_name() {
		return 'qb-contact-form';
	}

	/** @return string */
	public function get_title() {
		return 'QB Contact Form';
	}

	/** @return string */
	public function get_icon() {
		return 'eicon-form-horizontal';
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
		$this->qaf_text( 'title', 'عنوان النموذج', qaf_seed_page_copy( 'contact' )['form']['title'] );
		foreach ( array( 'brands' => 'بطاقة العلامات', 'promo' => 'بطاقة الشكاوى', 'map' => 'الخريطة' ) as $k => $l ) {
			$this->add_control( $k, array( 'label' => $l, 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'return_value' => 'yes' ) );
		}
		$this->add_control( 'note', array( 'type' => Controls_Manager::RAW_HTML, 'raw' => 'الإيميلات والنصوص من «خيارات الثيم ← النماذج والرسائل». الرسائل تُخزَّن في «الرسائل».' ) );
		$this->end_controls_section();
		$this->qaf_add_anim_section();
	}

	/** Render. */
	protected function render() {
		$s = $this->get_settings_for_display();
		qaf_section( 'contact-form', array( 'title' => $s['title'], 'brands' => 'yes' === $s['brands'], 'promo' => 'yes' === $s['promo'], 'map' => 'yes' === $s['map'] ) + $this->qaf_base_args( $s ) );
	}
}
