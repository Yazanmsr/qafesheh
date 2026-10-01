<?php
/**
 * Content types: companies, services, branches, jobs, complaints + brand taxonomy.
 * Slugs come from options; rewrite flush runs only when flagged.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

/**
 * Label helper.
 *
 * @param string $single Singular.
 * @param string $plural Plural.
 * @return array
 */
function qaf_pt_labels( $single, $plural ) {
	return array(
		'name'               => $plural,
		'singular_name'      => $single,
		'menu_name'          => $plural,
		'all_items'          => 'كل ' . $plural,
		'add_new'            => 'إضافة جديد',
		'add_new_item'       => 'إضافة ' . $single,
		'edit_item'          => 'تعديل ' . $single,
		'new_item'           => $single . ' جديد',
		'view_item'          => 'عرض ' . $single,
		'search_items'       => 'بحث',
		'not_found'          => 'لا يوجد عناصر',
		'not_found_in_trash' => 'لا يوجد عناصر في السلة',
	);
}

/**
 * Register post types.
 */
function qaf_register_post_types() {
	$base = array(
		'public'       => true,
		'show_in_rest' => true,
		'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
	);

	$types = array(
		'qaf_company' => array( 'شركة', 'الشركات', 'dashicons-building', 'slug_company', 21 ),
		'qaf_service' => array( 'خدمة', 'الخدمات', 'dashicons-admin-tools', 'slug_service', 22 ),
		'qaf_branch'  => array( 'فرع', 'الفروع', 'dashicons-location', 'slug_branch', 23 ),
		'qaf_job'     => array( 'وظيفة', 'الوظائف', 'dashicons-id-alt', 'slug_job', 24 ),
	);

	foreach ( $types as $type => $def ) {
		$slug = sanitize_title( qaf_opt( $def[3] ) );
		register_post_type(
			$type,
			array_merge(
				$base,
				array(
					'labels'        => qaf_pt_labels( $def[0], $def[1] ),
					'menu_icon'     => $def[2],
					'menu_position' => $def[4],
					'has_archive'   => $slug,
					'rewrite'       => array( 'slug' => $slug, 'with_front' => false ),
				)
			)
		);
	}

}
add_action( 'init', 'qaf_register_post_types' );

/**
 * Register taxonomies.
 */
function qaf_register_taxonomies() {
	register_taxonomy(
		'qaf_brand',
		array( 'qaf_company', 'qaf_service', 'qaf_branch', 'qaf_job', 'post' ),
		array(
			'labels'            => array(
				'name'          => 'العلامات التجارية',
				'singular_name' => 'علامة',
				'menu_name'     => 'العلامات',
				'all_items'     => 'كل العلامات',
				'add_new_item'  => 'إضافة علامة',
				'edit_item'     => 'تعديل علامة',
			),
			'public'            => true,
			'hierarchical'      => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'brand', 'with_front' => false ),
		)
	);

	$job_tax = array(
		'qaf_job_dept' => array( 'الأقسام', 'قسم', 'job-department' ),
		'qaf_job_loc'  => array( 'مواقع العمل', 'موقع', 'job-location' ),
		'qaf_job_type' => array( 'أنواع الدوام', 'نوع دوام', 'job-type' ),
	);
	foreach ( $job_tax as $tax => $def ) {
		register_taxonomy(
			$tax,
			array( 'qaf_job' ),
			array(
				'labels'            => array(
					'name'          => $def[0],
					'singular_name' => $def[1],
					'add_new_item'  => 'إضافة ' . $def[1],
					'edit_item'     => 'تعديل ' . $def[1],
				),
				'public'            => true,
				'publicly_queryable' => false,
				'hierarchical'      => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array( 'slug' => $def[2], 'with_front' => false ),
			)
		);
	}

	register_taxonomy(
		'qaf_service_cat',
		array( 'qaf_service' ),
		array(
			'labels'            => array(
				'name'          => 'أقسام الخدمات',
				'singular_name' => 'قسم',
			),
			'public'            => true,
			'hierarchical'      => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'service-category', 'with_front' => false ),
		)
	);
}
add_action( 'init', 'qaf_register_taxonomies' );

/**
 * Posts are "Articles & News" in this site.
 */
function qaf_rename_posts() {
	global $wp_post_types;
	if ( empty( $wp_post_types['post'] ) ) {
		return;
	}
	$l                = $wp_post_types['post']->labels;
	$l->name          = 'المقالات والأخبار';
	$l->menu_name     = 'المقالات والأخبار';
	$l->singular_name = 'مقال';
	$l->all_items     = 'كل المقالات';
	$l->add_new_item  = 'إضافة مقال';
	$l->edit_item     = 'تعديل مقال';
}
add_action( 'init', 'qaf_rename_posts', 30 );

/**
 * Archive order & per-page from options.
 *
 * @param WP_Query $q Query.
 */
function qaf_archive_query( $q ) {
	if ( is_admin() || ! $q->is_main_query() ) {
		return;
	}
	if ( $q->is_post_type_archive( array( 'qaf_company', 'qaf_service', 'qaf_branch' ) ) ) {
		$q->set( 'posts_per_page', absint( qaf_opt( 'pp_archive' ) ) );
		$q->set( 'orderby', array( 'menu_order' => 'ASC', 'date' => 'DESC' ) );
	}
	/* Jobs are filtered client-side: load every open role on one page. */
	if ( $q->is_post_type_archive( 'qaf_job' ) ) {
		$q->set( 'posts_per_page', 100 );
		$q->set( 'orderby', array( 'menu_order' => 'ASC', 'date' => 'DESC' ) );
	}
	if ( $q->is_home() || $q->is_category() || $q->is_tag() || ( $q->is_search() && ! $q->get( 'post_type' ) ) ) {
		$q->set( 'posts_per_page', absint( qaf_opt( 'pp_blog' ) ) );
	}
	if ( $q->is_search() && isset( $_GET['post_type'] ) && 'post' === sanitize_key( wp_unslash( $_GET['post_type'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		$q->set( 'post_type', 'post' );
		$q->set( 'posts_per_page', absint( qaf_opt( 'pp_blog' ) ) );
	}
}
add_action( 'pre_get_posts', 'qaf_archive_query' );

/**
 * Flush rewrites once when flagged (slug change, activation, import).
 */
function qaf_maybe_flush_rewrites() {
	if ( get_option( 'qaf_flush_rewrite' ) ) {
		flush_rewrite_rules( false );
		delete_option( 'qaf_flush_rewrite' );
	}
}
add_action( 'init', 'qaf_maybe_flush_rewrites', 99 );

/**
 * Flag flush on theme switch.
 */
function qaf_on_switch_theme() {
	update_option( 'qaf_flush_rewrite', 1, false );
}
add_action( 'after_switch_theme', 'qaf_on_switch_theme' );

/**
 * Branches have no page of their own — send visitors to the branch in the list.
 */
function qaf_branch_redirect() {
	if ( is_singular( 'qaf_branch' ) ) {
		$post = get_queried_object();
		wp_safe_redirect( qaf_archive_url( 'qaf_branch' ) . '#branch-' . $post->post_name, 301 );
		exit;
	}
}
add_action( 'template_redirect', 'qaf_branch_redirect' );
