<?php
/**
 * QB Capabilities.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Repeater;

/**
 * Framed image + spec rows + thumbnails.
 */
class QAF_Widget_Capabilities extends QAF_Widget_Base {

	/** @return string */
	public function get_name() {
		return 'qb-capabilities';
	}

	/** @return string */
	public function get_title() {
		return 'QB Capabilities';
	}

	/** @return string */
	public function get_icon() {
		return 'eicon-image-box';
	}

	/** Controls. */
	protected function register_controls() {
		$seed = $this->seed( 'capabilities' );
		$this->start_controls_section( 'content', array( 'label' => 'المحتوى' ) );
		$this->qaf_chapter_controls( $seed );
		$this->qaf_text( 'title', 'العنوان', $seed['title'] ?? '' );
		$this->qaf_media( 'image', 'الصورة الرئيسية', 'factory-women.jpg' );
		$this->qaf_media( 'thumb1', 'صورة مصغّرة ١', 'boutique-coats.jpg' );
		$this->qaf_media( 'thumb2', 'صورة مصغّرة ٢', 'textiles-stack.jpg' );
		$this->end_controls_section();

		$this->start_controls_section( 'rows_section', array( 'label' => 'جدول المواصفات' ) );
		$rep = new Repeater();
		$rep->add_control( 'label', array( 'label' => 'البند', 'type' => Controls_Manager::TEXT ) );
		$rep->add_control( 'value', array( 'label' => 'القيمة', 'type' => Controls_Manager::TEXT, 'label_block' => true ) );
		$this->add_control(
			'rows',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $rep->get_controls(),
				'default'     => $seed['rows'] ?? array(),
				'title_field' => '{{{ label }}}',
			)
		);
		$this->end_controls_section();

		$this->qaf_style_section( array( 'title' => array( 'العنوان', '.qaf-h2' ) ) );
		$this->qaf_add_anim_section();
	}

	/** Render. */
	protected function render() {
		$s = $this->get_settings_for_display();
		qaf_section(
			'capabilities',
			array(
				'num'    => $s['num'],
				'label'  => $s['label'],
				'title'  => $s['title'],
				'image'  => $this->qaf_media_value( $s['image'] ),
				'thumb1' => $this->qaf_media_value( $s['thumb1'] ),
				'thumb2' => $this->qaf_media_value( $s['thumb2'] ),
				'rows'   => (array) $s['rows'],
			) + $this->qaf_base_args( $s )
		);
	}
}
