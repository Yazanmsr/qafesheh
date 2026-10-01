<?php
/**
 * QB Services.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;

/**
 * Services hairline grid from the Services content type.
 */
class QAF_Widget_Services extends QAF_Widget_Base {

	/** @return string */
	public function get_name() {
		return 'qb-services';
	}

	/** @return string */
	public function get_title() {
		return 'QB Services';
	}

	/** @return string */
	public function get_icon() {
		return 'eicon-apps';
	}

	/** Controls. */
	protected function register_controls() {
		$seed = $this->seed( 'services' );
		$this->start_controls_section( 'content', array( 'label' => 'المحتوى' ) );
		$this->qaf_chapter_controls( $seed );
		$this->qaf_text( 'title', 'العنوان', $seed['title'] ?? '' );
		$this->qaf_text( 'link', 'نص الرابط', $seed['link'] ?? '' );
		$this->qaf_url( 'link_url', 'الرابط (فارغ = أرشيف الخدمات)' );
		$this->add_control( 'count', array( 'label' => 'عدد الخدمات', 'type' => Controls_Manager::NUMBER, 'min' => 1, 'max' => 12, 'default' => 6 ) );
		$this->end_controls_section();

		$this->qaf_style_section(
			array(
				'title' => array( 'العنوان', '.qaf-h2' ),
				'card'  => array( 'عنوان الخدمة', '.qaf-svc__title' ),
			)
		);
		$this->qaf_add_anim_section();
	}

	/** Render. */
	protected function render() {
		$s    = $this->get_settings_for_display();
		$link = $this->qaf_link_value( $s['link_url'] );
		qaf_section(
			'services',
			array(
				'num'      => $s['num'],
				'label'    => $s['label'],
				'title'    => $s['title'],
				'link'     => $s['link'],
				'link_url' => $link ? $link : qaf_archive_url( 'qaf_service' ),
				'count'    => absint( $s['count'] ),
			) + $this->qaf_base_args( $s )
		);
	}
}
