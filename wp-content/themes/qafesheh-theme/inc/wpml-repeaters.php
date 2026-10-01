<?php
/**
 * WPML Elementor repeater integrations. Loaded only when WPML's base class exists.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

/**
 * Shared behaviour.
 */
abstract class QAF_WPML_Items_Base extends WPML_Elementor_Module_With_Items {

	/**
	 * Field definitions: key => [ title, editor type ].
	 *
	 * @return array
	 */
	abstract protected function defs();

	/**
	 * Fields.
	 *
	 * @return string[]
	 */
	public function get_fields() {
		return array_keys( $this->defs() );
	}

	/**
	 * Title.
	 *
	 * @param string $field Field.
	 * @return string
	 */
	protected function get_title( $field ) {
		return $this->defs()[ $field ][0] ?? $field;
	}

	/**
	 * Editor type.
	 *
	 * @param string $field Field.
	 * @return string
	 */
	protected function get_editor_type( $field ) {
		return $this->defs()[ $field ][1] ?? 'LINE';
	}
}

/**
 * Hero stats.
 */
class QAF_WPML_Hero_Stats extends QAF_WPML_Items_Base {
	/** @return string */
	public function get_items_field() {
		return 'stats';
	}
	/** @return array */
	protected function defs() {
		return array(
			'num'   => array( 'Hero stat: number', 'LINE' ),
			'label' => array( 'Hero stat: label', 'LINE' ),
		);
	}
}

/**
 * Capabilities spec rows.
 */
class QAF_WPML_Spec_Rows extends QAF_WPML_Items_Base {
	/** @return string */
	public function get_items_field() {
		return 'rows';
	}
	/** @return array */
	protected function defs() {
		return array(
			'label' => array( 'Spec: label', 'LINE' ),
			'value' => array( 'Spec: value', 'LINE' ),
		);
	}
}

/**
 * Standards rows.
 */
class QAF_WPML_Standard_Rows extends QAF_WPML_Items_Base {
	/** @return string */
	public function get_items_field() {
		return 'rows';
	}
	/** @return array */
	protected function defs() {
		return array(
			'title' => array( 'Standard: title', 'LINE' ),
			'text'  => array( 'Standard: text', 'AREA' ),
		);
	}
}

/**
 * Timeline items.
 */
class QAF_WPML_Timeline_Items extends QAF_WPML_Items_Base {
	/** @return string */
	public function get_items_field() {
		return 'items';
	}
	/** @return array */
	protected function defs() {
		return array( 'year' => array( 'Timeline: year', 'LINE' ), 'title' => array( 'Timeline: title', 'LINE' ), 'text' => array( 'Timeline: text', 'AREA' ) );
	}
}

/**
 * Value items.
 */
class QAF_WPML_Value_Items extends QAF_WPML_Items_Base {
	/** @return string */
	public function get_items_field() {
		return 'items';
	}
	/** @return array */
	protected function defs() {
		return array( 'title' => array( 'Value: title', 'LINE' ), 'text' => array( 'Value: text', 'AREA' ) );
	}
}

/**
 * Step items.
 */
class QAF_WPML_Step_Items extends QAF_WPML_Items_Base {
	/** @return string */
	public function get_items_field() {
		return 'items';
	}
	/** @return array */
	protected function defs() {
		return array( 'title' => array( 'Step: title', 'LINE' ), 'text' => array( 'Step: text', 'AREA' ) );
	}
}

/**
 * Hero slides.
 */
class QAF_WPML_Hero_Slides extends QAF_WPML_Items_Base {
	/** @return string */
	public function get_items_field() {
		return 'slides';
	}
	/** @return array */
	protected function defs() {
		return array(
			'eyebrow'  => array( 'Hero slide: eyebrow', 'LINE' ),
			'title'    => array( 'Hero slide: title', 'AREA' ),
			'text'     => array( 'Hero slide: text', 'AREA' ),
			'btn1'     => array( 'Hero slide: button 1', 'LINE' ),
			'btn1_url' => array( 'Hero slide: button 1 link', 'LINK' ),
			'btn2'     => array( 'Hero slide: button 2', 'LINE' ),
			'btn2_url' => array( 'Hero slide: button 2 link', 'LINK' ),
		);
	}
}
