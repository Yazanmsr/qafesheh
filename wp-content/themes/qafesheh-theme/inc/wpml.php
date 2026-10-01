<?php
/**
 * WPML / Polylang integration.
 * - Options strings are translated inside the options panel (no String Translation needed).
 * - Every QB widget with copy is registered for Elementor translation.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

/**
 * Build a WPML fields LIST (associative maps fatal on PHP 8 in WPML's parser).
 *
 * @param array $map control => label | [ label, 'LINE'|'AREA'|'VISUAL'|'LINK' ].
 * @return array
 */
function qaf_wpml_fields( array $map ) {
	$fields = array();
	foreach ( $map as $control => $def ) {
		$def      = (array) $def;
		$fields[] = array(
			'field'       => $control,
			'type'        => $def[0],
			'editor_type' => $def[1] ?? 'LINE',
		);
	}
	return $fields;
}

/**
 * Widget registry for WPML: widget => [ fields, repeaters ].
 *
 * @return array
 */
function qaf_wpml_widget_map() {
	return array(
		'qb-hero'         => array( 'fields' => array() ),
		'qb-statement'    => array(
			'fields' => array(
				'num'      => 'Chapter number',
				'label'    => 'Chapter label',
				'title'    => array( 'Statement title', 'AREA' ),
				'col1'     => array( 'Statement column 1', 'AREA' ),
				'col2'     => array( 'Statement column 2', 'AREA' ),
				'link'     => 'Statement link text',
				'link_url' => array( 'Statement link', 'LINK' ),
			),
		),
		'qb-companies'    => array(
			'fields' => array( 'num' => 'Chapter number', 'label' => 'Chapter label', 'title' => 'Title', 'text' => array( 'Intro', 'AREA' ) ),
		),
		'qb-capabilities' => array(
			'fields'    => array( 'num' => 'Chapter number', 'label' => 'Chapter label', 'title' => 'Title' ),
			'repeaters' => array( 'rows' => array( 'label' => 'Spec label', 'value' => 'Spec value' ) ),
		),
		'qb-services'     => array(
			'fields' => array( 'num' => 'Chapter number', 'label' => 'Chapter label', 'title' => 'Title', 'link' => 'Link text', 'link_url' => array( 'Link', 'LINK' ) ),
		),
		'qb-standards'    => array(
			'fields'    => array( 'num' => 'Chapter number', 'label' => 'Chapter label' ),
			'repeaters' => array( 'rows' => array( 'title' => 'Standard title', 'text' => array( 'Standard text', 'AREA' ) ) ),
		),
		'qb-posts'        => array(
			'fields' => array( 'num' => 'Chapter number', 'label' => 'Chapter label', 'title' => 'Title', 'link' => 'Link text', 'read' => 'Read text' ),
		),
		'qb-branches'     => array(
			'fields' => array( 'num' => 'Chapter number', 'label' => 'Chapter label', 'title' => array( 'Title', 'AREA' ) ),
		),
		'qb-page-hero'    => array( 'fields' => array( 'title' => array( 'Page hero: title', 'AREA' ), 'text' => array( 'Page hero: text', 'AREA' ), 'ghost' => 'Page hero: ghost word' ) ),
		'qb-story'        => array( 'fields' => array( 'kicker' => 'Story: kicker', 'title' => array( 'Story: title', 'AREA' ), 'text1' => array( 'Story: paragraph 1', 'AREA' ), 'text2' => array( 'Story: paragraph 2', 'AREA' ) ) ),
		'qb-timeline'     => array( 'fields' => array( 'kicker' => 'Timeline: kicker', 'title' => 'Timeline: title' ) ),
		'qb-vision'       => array( 'fields' => array( 'v_title' => 'Vision: title', 'v_text' => array( 'Vision: text', 'AREA' ), 'm_title' => 'Mission: title', 'm_text' => array( 'Mission: text', 'AREA' ) ) ),
		'qb-values'       => array( 'fields' => array( 'kicker' => 'Values: kicker', 'title' => 'Values: title' ) ),
		'qb-channels'     => array( 'fields' => array( 'email_title' => 'Channels: email title', 'phone_title' => 'Channels: phone title', 'address_title' => 'Channels: address title' ) ),
		'qb-steps-bar'    => array( 'fields' => array() ),
		'qb-contact-form' => array( 'fields' => array( 'title' => 'Contact form: title' ) ),
		'qb-complaint-form' => array( 'fields' => array( 'title' => 'Complaint form: title' ) ),
		'qb-cta'          => array(
			'fields' => array(
				'title'    => 'CTA title',
				'text'     => array( 'CTA text', 'AREA' ),
				'btn1'     => 'CTA button 1',
				'btn1_url' => array( 'CTA button 1 link', 'LINK' ),
				'btn2'     => 'CTA button 2',
				'btn2_url' => array( 'CTA button 2 link', 'LINK' ),
			),
		),
	);
}

/**
 * Repeater integration classes per widget.
 *
 * @return array widget => class[]
 */
function qaf_wpml_repeater_classes() {
	return array(
		'qb-hero'         => array( 'QAF_WPML_Hero_Stats', 'QAF_WPML_Hero_Slides' ),
		'qb-capabilities' => array( 'QAF_WPML_Spec_Rows' ),
		'qb-standards'    => array( 'QAF_WPML_Standard_Rows' ),
		'qb-timeline'     => array( 'QAF_WPML_Timeline_Items' ),
		'qb-values'       => array( 'QAF_WPML_Value_Items' ),
		'qb-steps-bar'    => array( 'QAF_WPML_Step_Items' ),
	);
}

/**
 * Register widgets for WPML Elementor translation.
 *
 * @param array $widgets Widgets.
 * @return array
 */
function qaf_wpml_widgets( $widgets ) {
	$has_items = class_exists( 'WPML_Elementor_Module_With_Items' );
	if ( $has_items ) {
		require_once QAF_DIR . 'inc/wpml-repeaters.php';
	}
	$classes = qaf_wpml_repeater_classes();

	foreach ( qaf_wpml_widget_map() as $name => $def ) {
		$entry = array(
			'conditions' => array( 'widgetType' => $name ),
			'fields'     => qaf_wpml_fields( $def['fields'] ),
		);
		if ( $has_items && ! empty( $classes[ $name ] ) ) {
			$entry['integration-class'] = $classes[ $name ];
		}
		$widgets[ $name ] = $entry;
	}
	return $widgets;
}
add_filter( 'wpml_elementor_widgets_to_translate', 'qaf_wpml_widgets' );

/**
 * Ensure dir="rtl" on <html> for RTL languages even when a plugin misreports.
 *
 * @param string $output Attributes.
 * @return string
 */
function qaf_language_attributes( $output ) {
	if ( is_rtl() && false === strpos( $output, 'dir=' ) ) {
		$output .= ' dir="rtl"';
	}
	return $output;
}
add_filter( 'language_attributes', 'qaf_language_attributes' );
