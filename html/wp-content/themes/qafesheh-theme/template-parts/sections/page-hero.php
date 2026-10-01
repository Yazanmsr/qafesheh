<?php
/**
 * Page hero section (Elementor / page fallback) — wraps the shared page hero.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$a = wp_parse_args( $args ?? array(), array( 'title' => get_the_title(), 'text' => '', 'align' => 'start', 'ghost' => '' ) );
get_template_part( 'template-parts/header/page-hero', null, array( 'variant' => 'stack' ) + $a );
