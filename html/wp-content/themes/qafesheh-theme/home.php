<?php
/**
 * Articles & news index (posts page).
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

get_header();
get_template_part( 'template-parts/blog/listing' );
get_footer();
