<?php
/**
 * QB Branches.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;

/**
 * Branch list + live map.
 */
class QAF_Widget_Branches extends QAF_Widget_Base {

	/** @return string */
	public function get_name() {
		return 'qb-branches';
	}

	/** @return string */
	public function get_title() {
		return 'QB Branches';
	}

	/** @return string */
	public function get_icon() {
		return 'eicon-google-maps';
	}

	/** Controls. */
	protected function register_controls() {
		$seed   = $this->seed( 'branches' );
		$brands = array( '' => 'كل العلامات' );
		foreach ( get_terms( array( 'taxonomy' => 'qaf_brand', 'hide_empty' => false ) ) as $term ) {
			if ( ! is_wp_error( $term ) ) {
				$brands[ $term->slug ] = $term->name;
			}
		}

		$this->start_controls_section( 'content', array( 'label' => 'المحتوى' ) );
		$this->qaf_chapter_controls( $seed );
		$this->qaf_text( 'title', 'العنوان', $seed['title'] ?? '', 'TEXTAREA' );
		$this->add_control( 'count', array( 'label' => 'عدد الفروع', 'type' => Controls_Manager::NUMBER, 'min' => 1, 'max' => 20, 'default' => 5 ) );
		$this->add_control( 'brand', array( 'label' => 'فلترة بالعلامة', 'type' => Controls_Manager::SELECT, 'options' => $brands, 'default' => '' ) );
		$this->end_controls_section();

		$this->qaf_style_section(
			array(
				'title' => array( 'العنوان', '.qaf-h2' ),
				'name'  => array( 'اسم الفرع', '.qaf-branch__name' ),
			)
		);
		$this->qaf_add_anim_section();
	}

	/** Render. */
	protected function render() {
		$s = $this->get_settings_for_display();
		qaf_section(
			'branches',
			array(
				'num'   => $s['num'],
				'label' => $s['label'],
				'title' => $s['title'],
				'count' => absint( $s['count'] ),
				'brand' => sanitize_title( (string) $s['brand'] ),
			) + $this->qaf_base_args( $s )
		);
	}
}
