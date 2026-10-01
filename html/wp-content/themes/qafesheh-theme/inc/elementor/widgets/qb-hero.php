<?php
/**
 * QB Hero (slider).
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Repeater;

/**
 * Hero slider with a fixed data plinth.
 */
class QAF_Widget_Hero extends QAF_Widget_Base {

	/** @return string */
	public function get_name() {
		return 'qb-hero';
	}

	/** @return string */
	public function get_title() {
		return 'QB Hero Slider';
	}

	/** @return string */
	public function get_icon() {
		return 'eicon-slider-full-screen';
	}

	/** @return string[] */
	public function get_script_depends() {
		return array( 'qaf-ui', 'qaf-hero' );
	}

	/**
	 * Seed slides converted to Elementor defaults.
	 *
	 * @param array $slides Seed slides.
	 * @return array
	 */
	private function default_slides( $slides ) {
		return array_map(
			static function ( $s ) {
				return array(
					'image'    => array( 'url' => qaf_image_url( $s['image'] ), 'id' => qaf_imported_media_id( $s['image'] ) ),
					'position' => $s['position'],
					'eyebrow'  => $s['eyebrow'],
					'title'    => $s['title'],
					'text'     => $s['text'],
					'btn1'     => $s['btn1'],
					'btn1_url' => array( 'url' => $s['btn1_url'] ),
					'btn2'     => $s['btn2'],
					'btn2_url' => array( 'url' => $s['btn2_url'] ),
				);
			},
			$slides
		);
	}

	/** Controls. */
	protected function register_controls() {
		$seed = $this->seed( 'hero' );

		$this->start_controls_section( 'slides_section', array( 'label' => 'الشرائح' ) );
		$rep = new Repeater();
		$rep->add_control( 'image', array( 'label' => 'الصورة', 'type' => Controls_Manager::MEDIA, 'description' => '2560×1350 تقريباً. الشريحة الأولى أهم صورة بالموقع للأداء.' ) );
		$rep->add_control(
			'position',
			array(
				'label'   => 'تموضع الصورة',
				'type'    => Controls_Manager::SELECT,
				'default' => 'center',
				'options' => array(
					'center'        => 'وسط',
					'left center'   => 'يسار',
					'right center'  => 'يمين',
					'center top'    => 'أعلى',
					'center bottom' => 'أسفل',
				),
			)
		);
		$rep->add_control( 'eyebrow', array( 'label' => 'السطر العلوي', 'type' => Controls_Manager::TEXT, 'label_block' => true ) );
		$rep->add_control( 'title', array( 'label' => 'العنوان (Enter = سطر جديد)', 'type' => Controls_Manager::TEXTAREA, 'rows' => 3 ) );
		$rep->add_control( 'text', array( 'label' => 'الوصف', 'type' => Controls_Manager::TEXTAREA, 'rows' => 3 ) );
		$rep->add_control( 'btn1', array( 'label' => 'الزر الأول', 'type' => Controls_Manager::TEXT ) );
		$rep->add_control( 'btn1_url', array( 'label' => 'رابط الزر الأول', 'type' => Controls_Manager::URL ) );
		$rep->add_control( 'btn2', array( 'label' => 'الزر الثاني', 'type' => Controls_Manager::TEXT ) );
		$rep->add_control( 'btn2_url', array( 'label' => 'رابط الزر الثاني', 'type' => Controls_Manager::URL ) );
		$this->add_control(
			'slides',
			array(
				'label'       => 'الشرائح',
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $rep->get_controls(),
				'default'     => $this->default_slides( $seed['slides'] ?? array() ),
				'title_field' => '{{{ title }}}',
			)
		);
		$this->end_controls_section();

		$this->start_controls_section( 'behaviour', array( 'label' => 'الحركة والتشغيل' ) );
		$this->add_control( 'autoplay', array( 'label' => 'تشغيل تلقائي', 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'return_value' => 'yes', 'description' => 'يتوقف عند مرور الماوس، وعند إخفاء التبويب، وخارج الشاشة، ولمن يفضّل تقليل الحركة.' ) );
		$this->add_control( 'interval', array( 'label' => 'مدة الشريحة (ms)', 'type' => Controls_Manager::NUMBER, 'min' => 3500, 'max' => 15000, 'step' => 500, 'default' => 7000 ) );
		$this->add_control( 'grid', array( 'label' => 'الخطوط الرأسية', 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'return_value' => 'yes' ) );
		$this->end_controls_section();

		$this->start_controls_section( 'stats_section', array( 'label' => 'منصّة الأرقام (ثابتة)' ) );
		$srep = new Repeater();
		$srep->add_control( 'num', array( 'label' => 'الرقم', 'type' => Controls_Manager::TEXT, 'default' => '01' ) );
		$srep->add_control( 'suffix', array( 'label' => 'اللاحقة', 'type' => Controls_Manager::TEXT, 'default' => '' ) );
		$srep->add_control( 'label', array( 'label' => 'الوصف', 'type' => Controls_Manager::TEXT, 'default' => '', 'label_block' => true ) );
		$this->add_control(
			'stats',
			array(
				'label'       => 'الأرقام',
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $srep->get_controls(),
				'default'     => $seed['stats'] ?? array(),
				'title_field' => '{{{ num }}}{{{ suffix }}} — {{{ label }}}',
			)
		);
		$this->end_controls_section();

		$this->qaf_style_section(
			array(
				'title' => array( 'العنوان', '.qaf-hero__title' ),
				'text'  => array( 'الوصف', '.qaf-hero__text' ),
			)
		);
		$this->qaf_add_anim_section();
	}

	/** Render. */
	protected function render() {
		$s      = $this->get_settings_for_display();
		$slides = array();

		foreach ( (array) ( $s['slides'] ?? array() ) as $row ) {
			$slides[] = array(
				'image'    => $this->qaf_media_value( $row['image'] ?? array() ),
				'position' => $row['position'] ?? 'center',
				'eyebrow'  => $row['eyebrow'] ?? '',
				'title'    => $row['title'] ?? '',
				'text'     => $row['text'] ?? '',
				'btn1'     => $row['btn1'] ?? '',
				'btn1_url' => $this->qaf_link_value( $row['btn1_url'] ?? '' ),
				'btn2'     => $row['btn2'] ?? '',
				'btn2_url' => $this->qaf_link_value( $row['btn2_url'] ?? '' ),
			);
		}

		/* Heroes saved before v2.4 (single image + title at the top level) keep working. */
		if ( ! $slides && ( ! empty( $s['title'] ) || ! empty( $s['image'] ) ) ) {
			$slides[] = array(
				'image'    => $this->qaf_media_value( $s['image'] ?? array() ),
				'position' => $s['position'] ?? 'center',
				'eyebrow'  => $s['eyebrow'] ?? '',
				'title'    => $s['title'] ?? '',
				'text'     => $s['text'] ?? '',
				'btn1'     => $s['btn1'] ?? '',
				'btn1_url' => $this->qaf_link_value( $s['btn1_url'] ?? '' ),
				'btn2'     => $s['btn2'] ?? '',
				'btn2_url' => $this->qaf_link_value( $s['btn2_url'] ?? '' ),
			);
		}

		qaf_section(
			'hero',
			array(
				'slides'   => $slides,
				'stats'    => (array) ( $s['stats'] ?? array() ),
				'grid'     => 'yes' === ( $s['grid'] ?? 'yes' ),
				'autoplay' => 'yes' === ( $s['autoplay'] ?? 'yes' ),
				'interval' => absint( $s['interval'] ?? 7000 ),
			) + $this->qaf_base_args( $s )
		);
	}
}
