<?php
/**
 * Options → :root custom properties (one small inline block attached to qaf-base).
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

/**
 * Hex → "r,g,b".
 *
 * @param string $hex Hex colour.
 * @return string
 */
function qaf_hex_rgb( $hex ) {
	$hex = ltrim( (string) $hex, '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	if ( 6 !== strlen( $hex ) || ! ctype_xdigit( $hex ) ) {
		return '0,0,0';
	}
	return hexdec( substr( $hex, 0, 2 ) ) . ',' . hexdec( substr( $hex, 2, 2 ) ) . ',' . hexdec( substr( $hex, 4, 2 ) );
}

/**
 * Heading scale presets (clamp values).
 *
 * @return array
 */
function qaf_heading_scales() {
	return array(
		'compact'   => array( 'clamp(30px,4vw,56px)', 'clamp(24px,2.7vw,38px)', 'clamp(21px,2vw,28px)', 'clamp(19px,1.5vw,22px)' ),
		'balanced'  => array( 'clamp(32px,4.5vw,64px)', 'clamp(25px,3vw,42px)', 'clamp(22px,2.2vw,32px)', 'clamp(19px,1.6vw,24px)' ),
		'editorial' => array( 'clamp(34px,4.9vw,70px)', 'clamp(26px,3.2vw,46px)', 'clamp(22px,2.4vw,36px)', 'clamp(20px,1.8vw,26px)' ),
	);
}

/**
 * Build the :root token block.
 *
 * @return string
 */
function qaf_dynamic_css() {
	$scale = qaf_heading_scales()[ qaf_opt( 'h_scale' ) ] ?? qaf_heading_scales()['editorial'];

	$vars = array(
		'--qaf-primary'      => qaf_opt( 'c_primary' ),
		'--qaf-primary-rgb'  => qaf_hex_rgb( qaf_opt( 'c_primary' ) ),
		'--qaf-primary-dark' => qaf_opt( 'c_primary_dk' ),
		'--qaf-ink'          => qaf_opt( 'c_ink' ),
		'--qaf-ink-rgb'      => qaf_hex_rgb( qaf_opt( 'c_ink' ) ),
		'--qaf-dark'         => qaf_opt( 'c_dark' ),
		'--qaf-dark-rgb'     => qaf_hex_rgb( qaf_opt( 'c_dark' ) ),
		'--qaf-dark-2'       => qaf_opt( 'c_dark_2' ),
		'--qaf-bg'           => qaf_opt( 'c_bg' ),
		'--qaf-bg-rgb'       => qaf_hex_rgb( qaf_opt( 'c_bg' ) ),
		'--qaf-bg-soft'      => qaf_opt( 'c_bg_soft' ),
		'--qaf-line'         => qaf_opt( 'c_line' ),
		'--qaf-body'         => qaf_opt( 'c_body' ),
		'--qaf-muted'        => qaf_opt( 'c_muted' ),
		'--qaf-on-dark'      => qaf_opt( 'c_on_dark' ),
		'--qaf-b-qafesheh'   => qaf_opt( 'c_b_qafesheh' ),
		'--qaf-b-aljazi'     => qaf_opt( 'c_b_aljazi' ),
		'--qaf-b-makarem'    => qaf_opt( 'c_b_makarem' ),
		'--qaf-container'    => absint( qaf_opt( 'container' ) ) . 'px',
		'--qaf-radius'       => absint( qaf_opt( 'radius' ) ) . 'px',
		'--qaf-fs-base'      => (float) qaf_opt( 'fs_base' ) . 'px',
		'--qaf-body-weight'  => absint( qaf_opt( 'body_weight' ) ),
		'--qaf-h1'           => $scale[0],
		'--qaf-h2'           => $scale[1],
		'--qaf-h3'           => $scale[2],
		'--qaf-h4'           => $scale[3],
		'--qaf-logo-h'       => absint( qaf_opt( 'logo_h' ) ) . 'px',
		'--qaf-logo-h-m'     => absint( qaf_opt( 'logo_h_m' ) ) . 'px',
		'--qaf-banner-h'     => absint( qaf_opt( 'banner_h' ) ) . 'px',
		'--qaf-banner-h-m'   => absint( qaf_opt( 'banner_h_m' ) ) . 'px',
		'--qaf-banner-overlay' => (float) qaf_opt( 'banner_overlay' ),
		'--qaf-anim-dur'     => absint( qaf_opt( 'anim_dur' ) ) . 'ms',
		'--qaf-anim-dist'    => absint( qaf_opt( 'anim_dist' ) ) . 'px',
		'--qaf-anim-stagger' => absint( qaf_opt( 'anim_stagger' ) ) . 'ms',
	);

	$css = ':root{';
	foreach ( $vars as $name => $value ) {
		$value = preg_replace( '/[^a-zA-Z0-9#%().,\-\s]/', '', (string) $value );
		$css  .= $name . ':' . $value . ';';
	}
	return $css . '}';
}
