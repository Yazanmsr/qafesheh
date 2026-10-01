<?php
/**
 * QB Posts.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;

/**
 * Latest articles as editorial rows.
 */
class QAF_Widget_Posts extends QAF_Widget_Base {

	/** @return string */
	public function get_name() {
		return 'qb-posts';
	}

	/** @return string */
	public function get_title() {
		return 'QB Posts';
	}

	/** @return string */
	public function get_icon() {
		return 'eicon-post-list';
	}

	/** Controls. */
	protected function register_controls() {
		$seed = $this->seed( 'news' );
		$cats = array( 0 => 'كل التصنيفات' );
		foreach ( get_categories( array( 'hide_empty' => false ) ) as $cat ) {
			$cats[ $cat->term_id ] = $cat->name;
		}

		$this->start_controls_section( 'content', array( 'label' => 'المحتوى' ) );
		$this->qaf_chapter_controls( $seed );
		$this->qaf_text( 'title', 'العنوان', $seed['title'] ?? '' );
		$this->qaf_text( 'link', 'نص رابط الأرشيف', $seed['link'] ?? '' );
		$this->qaf_text( 'read', 'نص «اقرأ»', qaf_opt_i18n( 'str_read' ) );
		$this->add_control( 'count', array( 'label' => 'العدد', 'type' => Controls_Manager::NUMBER, 'min' => 1, 'max' => 12, 'default' => 3 ) );
		$this->add_control( 'category', array( 'label' => 'التصنيف', 'type' => Controls_Manager::SELECT, 'options' => $cats, 'default' => 0 ) );
		$this->end_controls_section();

		$this->qaf_style_section(
			array(
				'title' => array( 'العنوان', '.qaf-h2' ),
				'item'  => array( 'عنوان المقال', '.qaf-news__title' ),
			)
		);
		$this->qaf_add_anim_section();
	}

	/** Render. */
	protected function render() {
		$s = $this->get_settings_for_display();
		qaf_section(
			'news',
			array(
				'num'      => $s['num'],
				'label'    => $s['label'],
				'title'    => $s['title'],
				'link'     => $s['link'],
				'read'     => $s['read'],
				'count'    => absint( $s['count'] ),
				'category' => absint( $s['category'] ),
			) + $this->qaf_base_args( $s )
		);
	}
}
