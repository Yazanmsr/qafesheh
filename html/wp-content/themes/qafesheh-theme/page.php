<?php
/**
 * Page. Elementor pages render full width; classic pages use the reading column.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) {
	the_post();
	if ( qaf_is_elementor( get_the_ID() ) ) {
		the_content();
	} elseif ( qaf_page_recipe( get_the_ID() ) ) {
		qaf_render_page_recipe( get_the_ID() );
	} else {
		get_template_part( 'template-parts/content/content', 'page' );
	}
}

get_footer();
