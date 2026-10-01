<?php
/**
 * QB Contact Channels.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;

/**
 * Email / phone / head office from Theme Options.
 */
class QAF_Widget_Channels extends QAF_Widget_Base {

	/** @return string */
	public function get_name() {
		return 'qb-channels';
	}

	/** @return string */
	public function get_title() {
		return 'QB Contact Channels';
	}

	/** @return string */
	public function get_icon() {
		return 'eicon-mail';
	}

	/** Controls. */
	protected function register_controls() {
		$seed = qaf_seed_page_copy( 'contact' )['channels'];
		$this->start_controls_section( 'content', array( 'label' => 'المحتوى' ) );
		$this->add_control( 'note', array( 'type' => Controls_Manager::RAW_HTML, 'raw' => 'القيم من «خيارات الثيم ← التواصل والسوشال». هنا تعدّل العناوين فقط.' ) );
		$this->qaf_text( 'email_title', 'عنوان الإيميل', $seed['email_title'] );
		$this->qaf_text( 'phone_title', 'عنوان الهاتف', $seed['phone_title'] );
		$this->qaf_text( 'address_title', 'عنوان العنوان', $seed['address_title'] );
		$this->end_controls_section();
		$this->qaf_add_anim_section();
	}

	/** Render. */
	protected function render() {
		$s = $this->get_settings_for_display();
		qaf_section( 'channels', array( 'email_title' => $s['email_title'], 'phone_title' => $s['phone_title'], 'address_title' => $s['address_title'] ) + $this->qaf_base_args( $s ) );
	}
}
