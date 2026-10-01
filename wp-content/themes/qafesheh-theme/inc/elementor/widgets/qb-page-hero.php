<?php
/**
 * QB Page Hero.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;

/**
 * Dark page hero: breadcrumbs, gold title, intro, optional ghost word.
 */
class QAF_Widget_Page_Hero extends QAF_Widget_Base {

	/** @return string */
	public function get_name() {
		return 'qb-page-hero';
	}

	/** @return string */
	public function get_title() {
		return 'QB Page Hero';
	}

	/** @return string */
	public function get_icon() {
		return 'eicon-header';
	}

	/** Controls. */
	protected function register_controls() {
		$seed = qaf_seed_page_copy( 'about' )['hero'];
		$this->start_controls_section( 'content', array( 'label' => 'المحتوى' ) );
		$this->qaf_text( 'title', 'العنوان (فارغ = عنوان الصفحة)', '', 'TEXTAREA' );
		$this->qaf_text( 'text', 'النص', $seed['text'], 'TEXTAREA' );
		$this->add_control( 'align', array( 'label' => 'المحاذاة', 'type' => Controls_Manager::SELECT, 'default' => 'start', 'options' => array( 'start' => 'بداية السطر', 'center' => 'وسط' ) ) );
		$this->qaf_text( 'ghost', 'كلمة خلفية شفافة (اختياري)', '' );
		$this->end_controls_section();
		$this->qaf_style_section( array( 'title' => array( 'العنوان', '.qaf-phero__title' ), 'text' => array( 'النص', '.qaf-phero__text' ) ) );
		$this->qaf_add_anim_section();
	}

	/** Render. */
	protected function render() {
		$s = $this->get_settings_for_display();
		qaf_section( 'page-hero', array( 'title' => $s['title'] ? $s['title'] : get_the_title(), 'text' => $s['text'], 'align' => $s['align'], 'ghost' => $s['ghost'] ) );
	}
}
