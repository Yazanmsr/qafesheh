<?php
/**
 * Breadcrumb trail + JSON-LD. Hands off to Yoast / Rank Math when present.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

/**
 * Build the trail as [ label, url ] pairs.
 *
 * @return array
 */
function qaf_breadcrumb_trail() {
	$trail   = array( array( (string) qaf_opt_i18n( 'str_home' ), apply_filters( 'wpml_home_url', home_url( '/' ) ) ) );
	$object  = get_queried_object();

	if ( is_singular() && $object instanceof WP_Post ) {
		if ( 'post' === $object->post_type ) {
			$blog = (int) get_option( 'page_for_posts' );
			if ( $blog ) {
				$trail[] = array( get_the_title( (int) apply_filters( 'wpml_object_id', $blog, 'page', true ) ), qaf_blog_url() );
			}
		} elseif ( 'page' !== $object->post_type ) {
			$type = get_post_type_object( $object->post_type );
			if ( $type && $type->has_archive ) {
				$trail[] = array( qaf_archive_label( $object->post_type ), qaf_archive_url( $object->post_type ) );
			}
		}
		foreach ( array_reverse( get_post_ancestors( $object ) ) as $ancestor ) {
			$trail[] = array( get_the_title( $ancestor ), get_permalink( $ancestor ) );
		}
		$trail[] = array( get_the_title( $object ), '' );
	} elseif ( is_home() ) {
		$trail[] = array( get_the_title( (int) get_option( 'page_for_posts' ) ), '' );
	} elseif ( is_post_type_archive() ) {
		$trail[] = array( qaf_archive_label( (string) get_query_var( 'post_type' ) ), '' );
	} elseif ( is_category() || is_tag() || is_tax() ) {
		foreach ( array_reverse( get_ancestors( $object->term_id, $object->taxonomy, 'taxonomy' ) ) as $ancestor ) {
			$term    = get_term( $ancestor, $object->taxonomy );
			$trail[] = array( $term->name, get_term_link( $term ) );
		}
		$trail[] = array( single_term_title( '', false ), '' );
	} elseif ( is_search() ) {
		$trail[] = array( get_search_query(), '' );
	} elseif ( is_archive() ) {
		$trail[] = array( wp_strip_all_tags( get_the_archive_title() ), '' );
	}

	return apply_filters( 'qaf_breadcrumb_trail', $trail );
}

/**
 * Public label of a content-type archive (from the section-pages options, translated).
 *
 * @param string $type Post type.
 * @return string
 */
function qaf_archive_label( $type ) {
	$map = array(
		'qaf_company' => 'co_title',
		'qaf_service' => 'svc_title',
		'qaf_branch'  => 'br_title',
	);
	if ( isset( $map[ $type ] ) && qaf_opt_i18n( $map[ $type ] ) ) {
		return (string) qaf_opt_i18n( $map[ $type ] );
	}
	$obj = get_post_type_object( $type );
	return $obj ? $obj->labels->name : '';
}

/**
 * Print breadcrumbs.
 */
function qaf_breadcrumbs() {
	if ( ! qaf_opt( 'crumbs' ) ) {
		return;
	}
	if ( function_exists( 'yoast_breadcrumb' ) && apply_filters( 'qaf_use_seo_breadcrumbs', false ) ) {
		yoast_breadcrumb( '<nav class="qaf-crumbs" aria-label="breadcrumb">', '</nav>' );
		return;
	}
	if ( function_exists( 'rank_math_the_breadcrumbs' ) && apply_filters( 'qaf_use_seo_breadcrumbs', false ) ) {
		rank_math_the_breadcrumbs();
		return;
	}

	$trail = qaf_breadcrumb_trail();
	$items = array();
	$ld    = array();

	foreach ( $trail as $i => $crumb ) {
		$label = esc_html( wp_strip_all_tags( (string) $crumb[0] ) );
		$items[] = $crumb[1] ? '<a href="' . esc_url( $crumb[1] ) . '">' . $label . '</a>' : '<span aria-current="page">' . $label . '</span>';
		$ld[]    = array_filter(
			array(
				'@type'    => 'ListItem',
				'position' => $i + 1,
				'name'     => wp_strip_all_tags( (string) $crumb[0] ),
				'item'     => $crumb[1] ? $crumb[1] : null,
			)
		);
	}

	echo '<nav class="qaf-crumbs" aria-label="breadcrumb">' . implode( '<span class="qaf-crumbs__sep" aria-hidden="true">✦</span>', $items ) . '</nav>'; // phpcs:ignore WordPress.Security.EscapeOutput
	printf(
		'<script type="application/ld+json">%s</script>',
		wp_json_encode( array( '@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $ld ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES )
	);
}
