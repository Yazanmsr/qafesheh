<?php
/**
 * QB Statement.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

/**
 * Chapter statement with two columns and a link.
 */
class QAF_Widget_Statement extends QAF_Widget_Base {

	/** @return string */
	public function get_name() {
		return 'qb-statement';
	}

	/** @return string */
	public function get_title() {
		return 'QB Statement';
	}

	/** @return string */
	public function get_icon() {
		return 'eicon-text';
	}

	/** Controls. */
	protected function register_controls() {
		$seed = $this->seed( 'statement' );
		$this->start_controls_section( 'content', array( 'label' => 'المحتوى' ) );
		$this->qaf_chapter_controls( $seed );
		$this->qaf_text( 'title', 'العبارة الرئيسية', $seed['title'] ?? '', 'TEXTAREA' );
		$this->qaf_text( 'col1', 'العمود الأول', $seed['col1'] ?? '', 'TEXTAREA' );
		$this->qaf_text( 'col2', 'العمود الثاني', $seed['col2'] ?? '', 'TEXTAREA' );
		$this->qaf_text( 'link', 'نص الرابط', $seed['link'] ?? '' );
		$this->qaf_url( 'link_url', 'الرابط', $seed['link_url'] ?? '' );
		$this->end_controls_section();

		$this->qaf_style_section(
			array(
				'title' => array( 'العبارة', '.qaf-statement__title' ),
				'text'  => array( 'الأعمدة', '.qaf-statement__cols p' ),
			)
		);
		$this->qaf_add_anim_section();
	}

	/** Render. */
	protected function render() {
		$s = $this->get_settings_for_display();
		qaf_section(
			'statement',
			array(
				'num'      => $s['num'],
				'label'    => $s['label'],
				'title'    => $s['title'],
				'col1'     => $s['col1'],
				'col2'     => $s['col2'],
				'link'     => $s['link'],
				'link_url' => $this->qaf_link_value( $s['link_url'] ),
			) + $this->qaf_base_args( $s )
		);
	}
}
