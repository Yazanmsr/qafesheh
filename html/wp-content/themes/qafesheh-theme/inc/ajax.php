<?php
/**
 * AJAX endpoints. Nonce checked, inputs sanitised, post types whitelisted, numbers clamped.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

/**
 * Load more cards for archives.
 */
function qaf_ajax_load_posts() {
	check_ajax_referer( 'qaf_nonce', 'nonce' );

	$allowed = array( 'post', 'qaf_company', 'qaf_service', 'qaf_job' );
	$type    = isset( $_POST['type'] ) ? sanitize_key( wp_unslash( $_POST['type'] ) ) : 'post';
	if ( ! in_array( $type, $allowed, true ) ) {
		wp_send_json_error( array( 'message' => 'invalid' ), 400 );
	}
	$page     = isset( $_POST['page'] ) ? max( 1, min( 50, absint( $_POST['page'] ) ) ) : 1;
	$per_page = max( 3, min( 24, absint( qaf_opt( 'pp_archive' ) ) ) );

	$query = new WP_Query(
		array(
			'post_type'           => $type,
			'post_status'         => 'publish',
			'paged'               => $page,
			'posts_per_page'      => $per_page,
			'ignore_sticky_posts' => true,
			'suppress_filters'    => false,
		)
	);

	ob_start();
	while ( $query->have_posts() ) {
		$query->the_post();
		get_template_part( 'template-parts/content/card', 'post' );
	}
	wp_reset_postdata();

	wp_send_json_success(
		array(
			'html' => ob_get_clean(),
			'more' => $page < (int) $query->max_num_pages,
		)
	);
}
add_action( 'wp_ajax_qaf_load_posts', 'qaf_ajax_load_posts' );
add_action( 'wp_ajax_nopriv_qaf_load_posts', 'qaf_ajax_load_posts' );
