<?php
/**
 * Applications dashboard: columns, unread badge, status, filters, secure CV download, CSV export.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

/**
 * Status labels.
 *
 * @return array
 */
function qaf_app_status_labels() {
	return array(
		'new'         => 'جديد',
		'reviewed'    => 'تمت المراجعة',
		'shortlisted' => 'قائمة مختصرة',
		'rejected'    => 'مرفوض',
		'hired'       => 'تم التعيين',
	);
}

/**
 * Columns.
 *
 * @param array $cols Columns.
 * @return array
 */
function qaf_app_columns( $cols ) {
	return array(
		'cb'         => $cols['cb'],
		'title'      => 'المتقدّم',
		'qaf_job'    => 'الوظيفة',
		'qaf_phone'  => 'الهاتف',
		'qaf_email'  => 'الإيميل',
		'qaf_cv'     => 'السيرة',
		'qaf_status' => 'الحالة',
		'date'       => 'التاريخ',
	);
}
add_filter( 'manage_qaf_application_posts_columns', 'qaf_app_columns' );

/**
 * Column output.
 *
 * @param string $col     Column.
 * @param int    $post_id Post.
 */
function qaf_app_column( $col, $post_id ) {
	switch ( $col ) {
		case 'qaf_job':
			$job = absint( get_post_meta( $post_id, '_qaf_app_job', true ) );
			echo $job ? '<a href="' . esc_url( get_edit_post_link( $job ) ) . '">' . esc_html( get_the_title( $job ) ) . '</a>' : '<em>تقديم مفتوح</em>';
			break;
		case 'qaf_phone':
			$p = (string) get_post_meta( $post_id, '_qaf_app_phone', true );
			echo '<a href="' . esc_attr( qaf_tel( $p ) ) . '" dir="ltr">' . esc_html( $p ) . '</a>';
			break;
		case 'qaf_email':
			$e = (string) get_post_meta( $post_id, '_qaf_app_email', true );
			echo '<a href="mailto:' . esc_attr( $e ) . '">' . esc_html( $e ) . '</a>';
			break;
		case 'qaf_cv':
			if ( get_post_meta( $post_id, '_qaf_app_cv', true ) ) {
				echo '<a class="button button-small" href="' . esc_url( qaf_cv_download_url( $post_id ) ) . '">تحميل</a>';
			}
			break;
		case 'qaf_status':
			$s = (string) get_post_meta( $post_id, '_qaf_app_status', true );
			$l = qaf_app_status_labels();
			printf( '<span class="qaf-app-status qaf-app-status--%1$s">%2$s</span>', esc_attr( $s ), esc_html( $l[ $s ] ?? $s ) );
			if ( get_post_meta( $post_id, '_qaf_app_unread', true ) ) {
				echo ' <span class="qaf-app-dot" title="غير مقروء"></span>';
			}
			break;
	}
}
add_action( 'manage_qaf_application_posts_custom_column', 'qaf_app_column', 10, 2 );

/**
 * Secure CV URL.
 *
 * @param int $post_id Application.
 * @return string
 */
function qaf_cv_download_url( $post_id ) {
	return wp_nonce_url( admin_url( 'admin-post.php?action=qaf_cv&id=' . absint( $post_id ) ), 'qaf_cv_' . absint( $post_id ) );
}

/**
 * Stream a CV to an authorised user.
 */
function qaf_cv_download() {
	$id = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;
	if ( ! $id || ! current_user_can( 'edit_post', $id ) || 'qaf_application' !== get_post_type( $id ) ) {
		wp_die( 'غير مسموح', 403 );
	}
	check_admin_referer( 'qaf_cv_' . $id );

	$file = basename( (string) get_post_meta( $id, '_qaf_app_cv', true ) );
	$dir  = qaf_cv_dir();
	$path = $dir . $file;
	if ( ! $file || ! $dir || ! file_exists( $path ) || 0 !== strpos( realpath( $path ), realpath( $dir ) ) ) {
		wp_die( 'الملف غير موجود', 404 );
	}
	$ext   = strtolower( pathinfo( $file, PATHINFO_EXTENSION ) );
	$types = array( 'pdf' => 'application/pdf', 'doc' => 'application/msword', 'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' );
	$name  = sanitize_file_name( (string) get_post_meta( $id, '_qaf_app_name', true ) ) . '-cv.' . $ext;

	nocache_headers();
	header( 'Content-Type: ' . ( $types[ $ext ] ?? 'application/octet-stream' ) );
	header( 'Content-Disposition: attachment; filename="' . $name . '"' );
	header( 'Content-Length: ' . filesize( $path ) );
	header( 'X-Content-Type-Options: nosniff' );
	readfile( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	exit;
}
add_action( 'admin_post_qaf_cv', 'qaf_cv_download' );

/**
 * Details meta box + status control.
 */
function qaf_app_meta_box() {
	add_meta_box( 'qaf-app', 'بيانات الطلب', 'qaf_app_render_box', 'qaf_application', 'normal', 'high' );
	remove_meta_box( 'submitdiv', 'qaf_application', 'side' );
	add_meta_box( 'qaf-app-status', 'الحالة', 'qaf_app_render_status', 'qaf_application', 'side', 'high' );
}
add_action( 'add_meta_boxes', 'qaf_app_meta_box' );

/**
 * Render details (and mark as read).
 *
 * @param WP_Post $post Post.
 */
function qaf_app_render_box( $post ) {
	delete_post_meta( $post->ID, '_qaf_app_unread' );
	$job  = absint( get_post_meta( $post->ID, '_qaf_app_job', true ) );
	$dept = absint( get_post_meta( $post->ID, '_qaf_app_dept', true ) );
	$term = $dept ? get_term( $dept, 'qaf_job_dept' ) : null;
	$rows = array(
		'الاسم'   => esc_html( (string) get_post_meta( $post->ID, '_qaf_app_name', true ) ),
		'الوظيفة' => $job ? '<a href="' . esc_url( get_edit_post_link( $job ) ) . '">' . esc_html( get_the_title( $job ) ) . '</a>' : 'تقديم مفتوح',
		'القسم'   => $term && ! is_wp_error( $term ) ? esc_html( $term->name ) : '—',
		'الهاتف'  => '<a dir="ltr" href="' . esc_attr( qaf_tel( (string) get_post_meta( $post->ID, '_qaf_app_phone', true ) ) ) . '">' . esc_html( (string) get_post_meta( $post->ID, '_qaf_app_phone', true ) ) . '</a>',
		'الإيميل' => '<a href="mailto:' . esc_attr( (string) get_post_meta( $post->ID, '_qaf_app_email', true ) ) . '">' . esc_html( (string) get_post_meta( $post->ID, '_qaf_app_email', true ) ) . '</a>',
		'اللغة'   => esc_html( strtoupper( (string) get_post_meta( $post->ID, '_qaf_app_lang', true ) ) ),
		'التاريخ' => esc_html( get_the_date( 'Y-m-d H:i', $post ) ),
		'الرسالة' => nl2br( esc_html( (string) get_post_meta( $post->ID, '_qaf_app_message', true ) ) ),
	);
	echo '<table class="widefat striped qaf-app-table"><tbody>';
	foreach ( $rows as $label => $value ) {
		echo '<tr><th>' . esc_html( $label ) . '</th><td>' . $value . '</td></tr>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
	echo '</tbody></table>';
	if ( get_post_meta( $post->ID, '_qaf_app_cv', true ) ) {
		printf(
			'<p><a class="button button-primary" href="%s">تحميل السيرة الذاتية</a> <span class="description">%s — %s</span></p>',
			esc_url( qaf_cv_download_url( $post->ID ) ),
			esc_html( (string) get_post_meta( $post->ID, '_qaf_app_cv_name', true ) ),
			esc_html( size_format( (int) get_post_meta( $post->ID, '_qaf_app_cv_size', true ) ) )
		);
	}
}

/**
 * Status box.
 *
 * @param WP_Post $post Post.
 */
function qaf_app_render_status( $post ) {
	wp_nonce_field( 'qaf_app_status', 'qaf_app_status_nonce' );
	$current = (string) get_post_meta( $post->ID, '_qaf_app_status', true );
	echo '<select name="qaf_app_status" style="width:100%">';
	foreach ( qaf_app_status_labels() as $k => $l ) {
		printf( '<option value="%s"%s>%s</option>', esc_attr( $k ), selected( $current, $k, false ), esc_html( $l ) );
	}
	echo '</select><p><button class="button button-primary" type="submit" style="width:100%">حفظ</button></p>';
	printf( '<p><a class="submitdelete" style="color:#b32d2e" href="%s">حذف الطلب نهائياً (مع الملف)</a></p>', esc_url( get_delete_post_link( $post->ID, '', true ) ) );
}

/**
 * Save status.
 *
 * @param int $post_id Post.
 */
function qaf_app_save_status( $post_id ) {
	if ( ! isset( $_POST['qaf_app_status_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['qaf_app_status_nonce'] ) ), 'qaf_app_status' ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$status = isset( $_POST['qaf_app_status'] ) ? sanitize_key( wp_unslash( $_POST['qaf_app_status'] ) ) : 'new';
	if ( in_array( $status, QAF_APP_STATUSES, true ) ) {
		update_post_meta( $post_id, '_qaf_app_status', $status );
	}
}
add_action( 'save_post_qaf_application', 'qaf_app_save_status' );

/**
 * Keep applications private when saved from the edit screen.
 *
 * @param array $data Post data.
 * @return array
 */
function qaf_app_force_private( $data ) {
	if ( 'qaf_application' === $data['post_type'] && 'trash' !== $data['post_status'] ) {
		$data['post_status'] = 'private';
	}
	return $data;
}
add_filter( 'wp_insert_post_data', 'qaf_app_force_private' );

/**
 * Unread badge on the menu.
 */
function qaf_app_menu_badge() {
	global $submenu;
	$parent = 'edit.php?post_type=qaf_job';
	if ( empty( $submenu[ $parent ] ) ) {
		return;
	}
	$unread = (int) ( new WP_Query(
		array(
			'post_type'      => 'qaf_application',
			'post_status'    => 'private',
			'meta_key'       => '_qaf_app_unread', // phpcs:ignore WordPress.DB.SlowDBQuery
			'fields'         => 'ids',
			'posts_per_page' => 1,
		)
	) )->found_posts;
	if ( ! $unread ) {
		return;
	}
	foreach ( $submenu[ $parent ] as &$item ) {
		if ( 'edit.php?post_type=qaf_application' === $item[2] ) {
			$item[0] .= ' <span class="awaiting-mod"><span class="pending-count">' . absint( $unread ) . '</span></span>';
		}
	}
}
add_action( 'admin_menu', 'qaf_app_menu_badge', 99 );

/**
 * Filters above the list: job + status + export.
 *
 * @param string $post_type Post type.
 */
function qaf_app_filters( $post_type ) {
	if ( 'qaf_application' !== $post_type ) {
		return;
	}
	$jobs    = get_posts( array( 'post_type' => 'qaf_job', 'posts_per_page' => -1, 'post_status' => 'any', 'suppress_filters' => false ) );
	$current = isset( $_GET['qaf_job'] ) ? sanitize_text_field( wp_unslash( $_GET['qaf_job'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$status  = isset( $_GET['qaf_status'] ) ? sanitize_key( wp_unslash( $_GET['qaf_status'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification

	echo '<select name="qaf_job"><option value="">كل الوظائف</option><option value="open"' . selected( $current, 'open', false ) . '>تقديم مفتوح</option>';
	foreach ( $jobs as $job ) {
		printf( '<option value="%d"%s>%s</option>', (int) $job->ID, selected( $current, (string) $job->ID, false ), esc_html( $job->post_title ) );
	}
	echo '</select><select name="qaf_status"><option value="">كل الحالات</option>';
	foreach ( qaf_app_status_labels() as $k => $l ) {
		printf( '<option value="%s"%s>%s</option>', esc_attr( $k ), selected( $status, $k, false ), esc_html( $l ) );
	}
	echo '</select>';
	printf( '<a class="button" href="%s">تصدير CSV</a>', esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'qaf_app_export', 'qaf_job' => $current, 'qaf_status' => $status ), admin_url( 'admin-post.php' ) ), 'qaf_app_export' ) ) );
}
add_action( 'restrict_manage_posts', 'qaf_app_filters' );

/**
 * Meta query for the filters.
 *
 * @param array $args Filters.
 * @return array
 */
function qaf_app_meta_query( $args ) {
	$mq = array();
	if ( 'open' === $args['job'] ) {
		$mq[] = array( 'key' => '_qaf_app_job', 'value' => '0' );
	} elseif ( absint( $args['job'] ) ) {
		$mq[] = array( 'key' => '_qaf_app_job', 'value' => (string) absint( $args['job'] ) );
	}
	if ( in_array( $args['status'], QAF_APP_STATUSES, true ) ) {
		$mq[] = array( 'key' => '_qaf_app_status', 'value' => $args['status'] );
	}
	return $mq;
}

/**
 * Apply filters to the admin list.
 *
 * @param WP_Query $q Query.
 */
function qaf_app_filter_query( $q ) {
	if ( ! is_admin() || ! $q->is_main_query() || 'qaf_application' !== $q->get( 'post_type' ) ) {
		return;
	}
	// phpcs:disable WordPress.Security.NonceVerification
	$mq = qaf_app_meta_query(
		array(
			'job'    => isset( $_GET['qaf_job'] ) ? sanitize_text_field( wp_unslash( $_GET['qaf_job'] ) ) : '',
			'status' => isset( $_GET['qaf_status'] ) ? sanitize_key( wp_unslash( $_GET['qaf_status'] ) ) : '',
		)
	);
	// phpcs:enable
	if ( $mq ) {
		$q->set( 'meta_query', $mq ); // phpcs:ignore WordPress.DB.SlowDBQuery
	}
}
add_action( 'pre_get_posts', 'qaf_app_filter_query' );

/**
 * CSV cell safe against spreadsheet formula injection.
 *
 * @param string $value Value.
 * @return string
 */
function qaf_csv_safe( $value ) {
	$value = (string) $value;
	if ( '' !== $value && in_array( $value[0], array( '=', '+', '-', '@', "\t", "\r" ), true ) ) {
		$value = "'" . $value;
	}
	return $value;
}

/**
 * CSV export.
 */
function qaf_app_export() {
	if ( ! current_user_can( 'edit_others_posts' ) ) {
		wp_die( 'غير مسموح', 403 );
	}
	check_admin_referer( 'qaf_app_export' );

	$ids = get_posts(
		array(
			'post_type'      => 'qaf_application',
			'post_status'    => 'private',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_query'     => qaf_app_meta_query( // phpcs:ignore WordPress.DB.SlowDBQuery
				array(
					'job'    => isset( $_GET['qaf_job'] ) ? sanitize_text_field( wp_unslash( $_GET['qaf_job'] ) ) : '',
					'status' => isset( $_GET['qaf_status'] ) ? sanitize_key( wp_unslash( $_GET['qaf_status'] ) ) : '',
				)
			),
		)
	);

	nocache_headers();
	header( 'Content-Type: text/csv; charset=UTF-8' );
	header( 'Content-Disposition: attachment; filename="applications-' . gmdate( 'Y-m-d' ) . '.csv"' );
	$out = fopen( 'php://output', 'w' ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	fwrite( $out, "\xEF\xBB\xBF" ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	fputcsv( $out, array( 'Date', 'Name', 'Phone', 'Email', 'Job', 'Status', 'Language', 'Message' ) );
	$labels = qaf_app_status_labels();
	foreach ( $ids as $id ) {
		$job = absint( get_post_meta( $id, '_qaf_app_job', true ) );
		$st  = (string) get_post_meta( $id, '_qaf_app_status', true );
		fputcsv(
			$out,
			array_map(
				'qaf_csv_safe',
				array(
					get_the_date( 'Y-m-d H:i', $id ),
					get_post_meta( $id, '_qaf_app_name', true ),
					get_post_meta( $id, '_qaf_app_phone', true ),
					get_post_meta( $id, '_qaf_app_email', true ),
					$job ? get_the_title( $job ) : 'Open application',
					$labels[ $st ] ?? $st,
					get_post_meta( $id, '_qaf_app_lang', true ),
					get_post_meta( $id, '_qaf_app_message', true ),
				)
			)
		);
	}
	fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	exit;
}
add_action( 'admin_post_qaf_app_export', 'qaf_app_export' );

/**
 * Remove "Quick Edit" / "View" row actions that make no sense for applications.
 *
 * @param array   $actions Actions.
 * @param WP_Post $post    Post.
 * @return array
 */
function qaf_app_row_actions( $actions, $post ) {
	if ( 'qaf_application' === $post->post_type ) {
		unset( $actions['inline hide-if-no-js'], $actions['view'] );
		$actions['qaf_cv'] = '<a href="' . esc_url( qaf_cv_download_url( $post->ID ) ) . '">تحميل السيرة</a>';
	}
	return $actions;
}
add_filter( 'post_row_actions', 'qaf_app_row_actions', 10, 2 );
