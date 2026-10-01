<?php
/**
 * Company page: hero, intro, gallery, categories, quote + contact, sister brands.
 * Everything reads from the company entry — no page building needed per brand.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) {
	the_post();
	$qaf_color = qaf_company_color( get_the_ID() );
	echo '<div class="qaf-company" style="--qaf-accent:' . esc_attr( $qaf_color ) . ';--qaf-accent-light:' . esc_attr( qaf_color_on_light( $qaf_color ) ) . '">';
	foreach ( array( 'hero', 'intro', 'gallery', 'categories', 'contact', 'sisters' ) as $qaf_part ) {
		get_template_part( 'template-parts/company/' . $qaf_part );
	}
	echo '</div>';
}

get_footer();
