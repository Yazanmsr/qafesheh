<?php
/**
 * QB Offers — the three houses' running offers.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;

/**
 * Offers widget.
 */
class QAF_Widget_Offers extends QAF_Widget_Base {

	/** @return string */
	public function get_name() {
		return 'qb-offers';
	}

	/** @return string */
	public function get_title() {
		return 'QB Offers';
	}

	/** @return string */
	public function get_icon() {
		return 'eicon-price-list';
	}

	/** Controls. */
	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => 'المحتوى' ) );
		$this->qaf_chapter_controls( array( 'num' => '', 'label' => 'عروض الشركات' ) );
		$this->qaf_text( 'title', 'العنوان', "من بيوتنا الثلاثة،\nلفترة محدودة", 'TEXTAREA' );
		$this->qaf_text( 'text', 'المقدمة', '', 'TEXTAREA' );
		$this->add_control( 'limit', array( 'label' => 'أقصى عدد عروض', 'type' => Controls_Manager::NUMBER, 'min' => 1, 'max' => 9, 'default' => 3 ) );
		$this->add_control( 'note', array( 'type' => Controls_Manager::RAW_HTML, 'raw' => 'العروض من لوحة التحكم ← <b>العروض</b>. القسم بيظهر لحاله لما يكون في عرض شغّال، وبيختفي لما تنتهي العروض.' ) );
		$this->end_controls_section();
		$this->qaf_style_section(
			array(
				'title' => array( 'العنوان', '.qaf-h2' ),
				'card'  => array( 'عنوان العرض', '.qaf-offer__title' ),
			)
		);
		$this->qaf_add_anim_section();
		$this->update_control( 'anchor', array( 'default' => 'offers' ) );
	}

	/** Render. */
	protected function render() {
		$s = $this->get_settings_for_display();
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() && function_exists( 'qaf_offers_live' ) && ! qaf_offers_live() ) {
			echo '<div style="padding:32px;border:1px dashed #D6CDBD;text-align:center;color:#7a7263">قسم العروض — بيظهر هون تلقائياً لما يكون في عرض شغّال من «العروض» بلوحة التحكم.</div>';
			return;
		}
		qaf_section(
			'offers',
			array(
				'num'   => $s['num'],
				'label' => $s['label'],
				'title' => $s['title'],
				'text'  => $s['text'],
				'limit' => absint( $s['limit'] ),
			) + $this->qaf_base_args( $s )
		);
	}
}
