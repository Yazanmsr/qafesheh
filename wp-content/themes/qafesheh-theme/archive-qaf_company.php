<?php
/**
 * Companies archive: hero + brand cards + standards + CTA (same parts as the home page).
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

get_header();

get_template_part(
	'template-parts/header/page-hero',
	null,
	array(
		'title' => qaf_opt_i18n( 'co_title' ),
		'text'  => qaf_opt_i18n( 'co_text' ),
	)
);
qaf_section( 'companies', array( 'head' => false, 'plain' => true, 'count' => 12 ) );
qaf_section( 'standards' );
qaf_section( 'cta' );

get_footer();
