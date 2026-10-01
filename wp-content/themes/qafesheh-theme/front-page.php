<?php
/**
 * Front page.
 * Built with Elementor → Elementor output. Otherwise the same sections render from the seed copy,
 * through the exact template parts the QB widgets use.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( 'page' === get_option( 'show_on_front' ) && qaf_is_elementor() ) {
	while ( have_posts() ) {
		the_post();
		the_content();
	}
} else {
	foreach ( array( 'hero', 'statement', 'companies', 'offers', 'capabilities', 'services', 'standards', 'news', 'branches', 'cta' ) as $qaf_section ) {
		qaf_section( $qaf_section );
	}
}

get_footer();
