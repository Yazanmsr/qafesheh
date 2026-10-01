<?php
/**
 * Single entry. Articles use the editorial layout; other types fall back to the reading column.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) {
	the_post();
	if ( qaf_is_elementor( get_the_ID() ) ) {
		the_content();
	} elseif ( 'post' === get_post_type() ) {
		get_template_part( 'template-parts/blog/single' );
	} else {
		get_template_part( 'template-parts/content/content' );
	}
}

get_footer();
