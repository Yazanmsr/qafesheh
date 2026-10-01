<?php
/**
 * QB Story.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;


/**
 * Arched image beside a kicker, title and two paragraphs.
 */
class QAF_Widget_Story extends QAF_Widget_Base {

	/** @return string */
	public function get_name() {
		return 'qb-story';
	}

	/** @return string */
	public function get_title() {
		return 'QB Story';
	}

	/** @return string */
	public function get_icon() {
		return 'eicon-image-box';
	}

	/** Controls. */
	protected function register_controls() {
		$seed = qaf_seed_page_copy( 'about' )['story'];
		$this->start_controls_section( 'content', array( 'label' => 'المحتوى' ) );
		$this->qaf_media( 'image', 'الصورة', $seed['image'] );
		$this->qaf_text( 'kicker', 'السطر العلوي', $seed['kicker'] );
		$this->qaf_text( 'title', 'العنوان', $seed['title'], 'TEXTAREA' );
		$this->qaf_text( 'text1', 'الفقرة الأولى', $seed['text1'], 'TEXTAREA' );
		$this->qaf_text( 'text2', 'الفقرة الثانية', $seed['text2'], 'TEXTAREA' );
		$this->end_controls_section();
		$this->qaf_style_section( array( 'title' => array( 'العنوان', '.qaf-story__title' ), 'text' => array( 'الفقرات', '.qaf-story__text' ) ) );
		$this->qaf_add_anim_section();
	}

	/** Render. */
	protected function render() {
		$s = $this->get_settings_for_display();
		qaf_section( 'story', array( 'image' => $this->qaf_media_value( $s['image'] ), 'kicker' => $s['kicker'], 'title' => $s['title'], 'text1' => $s['text1'], 'text2' => $s['text2'] ) + $this->qaf_base_args( $s ) );
	}
}
