<?php
/**
 * SVG uploads — administrators only, sanitised through an allow-list.
 * Scripts, event handlers, foreign objects and external references are stripped.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

/**
 * Allow SVG mime for administrators.
 *
 * @param array $mimes Mimes.
 * @return array
 */
function qaf_svg_mimes( $mimes ) {
	if ( current_user_can( 'manage_options' ) ) {
		$mimes['svg'] = 'image/svg+xml';
	}
	return $mimes;
}
add_filter( 'upload_mimes', 'qaf_svg_mimes' );

/**
 * WP 5.1+ real mime check fix for SVG.
 *
 * @param array  $data     File data.
 * @param string $file     Path.
 * @param string $filename Name.
 * @return array
 */
function qaf_svg_filetype( $data, $file, $filename ) {
	if ( current_user_can( 'manage_options' ) && 'svg' === strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) ) ) {
		$data['ext']  = 'svg';
		$data['type'] = 'image/svg+xml';
	}
	return $data;
}
add_filter( 'wp_check_filetype_and_ext', 'qaf_svg_filetype', 10, 3 );

/**
 * Sanitise uploaded SVG in place.
 *
 * @param array $file Upload.
 * @return array
 */
function qaf_svg_prefilter( $file ) {
	if ( empty( $file['tmp_name'] ) || 'svg' !== strtolower( pathinfo( (string) $file['name'], PATHINFO_EXTENSION ) ) ) {
		return $file;
	}
	$raw   = file_get_contents( $file['tmp_name'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	$clean = false === $raw ? null : qaf_svg_clean( $raw );

	if ( null === $clean ) {
		$file['error'] = __( 'This SVG file is invalid or contains disallowed content.', 'qafesheh' );
		return $file;
	}
	file_put_contents( $file['tmp_name'], $clean ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	return $file;
}
add_filter( 'wp_handle_upload_prefilter', 'qaf_svg_prefilter' );

/**
 * Allow-list sanitiser.
 *
 * @param string $svg Markup.
 * @return string|null
 */
function qaf_svg_clean( $svg ) {
	if ( ! class_exists( 'DOMDocument' ) ) {
		return null;
	}

	$allowed_tags = array( 'svg', 'g', 'path', 'rect', 'circle', 'ellipse', 'line', 'polyline', 'polygon', 'text', 'tspan', 'defs', 'lineargradient', 'radialgradient', 'stop', 'clippath', 'mask', 'pattern', 'use', 'symbol', 'title', 'desc', 'style' );
	$allowed_attr = array( 'id', 'class', 'd', 'x', 'y', 'x1', 'x2', 'y1', 'y2', 'cx', 'cy', 'r', 'rx', 'ry', 'width', 'height', 'viewbox', 'fill', 'fill-rule', 'fill-opacity', 'clip-rule', 'stroke', 'stroke-width', 'stroke-linecap', 'stroke-linejoin', 'stroke-miterlimit', 'stroke-dasharray', 'stroke-opacity', 'opacity', 'transform', 'points', 'offset', 'stop-color', 'stop-opacity', 'gradientunits', 'gradienttransform', 'patternunits', 'patterntransform', 'clip-path', 'mask', 'xmlns', 'xmlns:xlink', 'version', 'preserveaspectratio', 'href', 'xlink:href', 'font-family', 'font-size', 'font-weight', 'text-anchor', 'style', 'role', 'aria-hidden', 'focusable' );

	$svg = preg_replace( '/<!DOCTYPE[^>]*>|<!ENTITY[^>]*>/i', '', (string) $svg );

	$prev = libxml_use_internal_errors( true );
	$dom  = new DOMDocument();
	$ok   = $dom->loadXML( $svg, LIBXML_NONET );
	libxml_clear_errors();
	libxml_use_internal_errors( $prev );

	if ( ! $ok || ! $dom->documentElement || 'svg' !== strtolower( $dom->documentElement->nodeName ) ) {
		return null;
	}

	$xpath = new DOMXPath( $dom );
	$nodes = iterator_to_array( $xpath->query( '//*' ) );

	foreach ( array_reverse( $nodes ) as $node ) {
		$tag = strtolower( $node->localName );
		if ( ! in_array( $tag, $allowed_tags, true ) ) {
			$node->parentNode && $node->parentNode->removeChild( $node );
			continue;
		}
		if ( 'style' === $tag && preg_match( '/@import|url\s*\(|expression/i', $node->textContent ) ) {
			$node->parentNode->removeChild( $node );
			continue;
		}
		for ( $i = $node->attributes->length - 1; $i >= 0; $i-- ) {
			$attr  = $node->attributes->item( $i );
			$name  = strtolower( $attr->nodeName );
			$value = trim( (string) $attr->nodeValue );

			$bad_name  = ! in_array( $name, $allowed_attr, true );
			$bad_value = (bool) preg_match( '/javascript:|data:text|<script|expression\s*\(/i', $value );
			$external  = in_array( $name, array( 'href', 'xlink:href' ), true ) && '' !== $value && '#' !== $value[0];

			if ( $bad_name || $bad_value || $external ) {
				$node->removeAttributeNode( $attr );
			}
		}
	}

	return $dom->saveXML( $dom->documentElement );
}
