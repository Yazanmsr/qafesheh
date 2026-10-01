<?php
/**
 * Form Entries screen: columns, unread badge, status, filters, secure attachment download, CSV export.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

/**
 * Status labels (admin).
 *
 * @return array
 */
function qaf_entry_status_labels() {
	return array(
		'new'      => 'جديدة',
		'progress' => 'قيد المتابعة',
		'resolved' => 'تم الحل',
		'closed'   => 'مغلقة',
	);
}

/**
 * Columns.
 *
 * @param array $cols Columns.
 * @return array
 */
function qaf_entry_columns( $cols ) {
	return array(
		'cb'           => $cols['cb'],
		'title'        => 'الرسالة',
		'qaf_form'     => 'النموذج',
		'qaf_contact'  => 'المرسل',
		'qaf_tracking' => 'رقم المتابعة',
		'qaf_status'   => 'الحالة',
		'date'         => 'التاريخ',
	);
}
add_filter( 'manage_qaf_entry_posts_columns', 'qaf_entry_columns' );

/**
 * Column output.
 *
 * @param string $col Column.
 * @param int    $id  Entry.
 */
function qaf_entry_column( $col, $id ) {
	$values = (array) get_post_meta( $id, '_qaf_e_values', true );
	switch ( $col ) {
		case 'qaf_form':
			$form  = (string) get_post_meta( $id, '_qaf_e_form', true );
			$forms = qaf_forms();
			$kind  = isset( $values['kind'], $forms['complaint']['fields']['kind'] ) ? ' · ' . qaf_form_display_value( $forms['complaint']['fields']['kind'], $values['kind'] ) : '';
			echo esc_html( ( $forms[ $form ]['label'] ?? $form ) . $kind );
			break;
		case 'qaf_contact':
			printf( '%s<br><a href="mailto:%2$s">%2$s</a><br><span dir="ltr">%3$s</span>', esc_html( $values['name'] ?? '' ), esc_attr( $values['email'] ?? '' ), esc_html( $values['phone'] ?? '' ) );
			break;
		case 'qaf_tracking':
			echo '<code>' . esc_html( (string) get_post_meta( $id, '_qaf_e_tracking', true ) ) . '</code>';
			break;
		case 'qaf_status':
			$s = (string) get_post_meta( $id, '_qaf_e_status', true );
			$l = qaf_entry_status_labels();
			printf( '<span class="qaf-app-status qaf-app-status--%1$s">%2$s</span>', esc_attr( 'progress' === $s ? 'shortlisted' : ( 'resolved' === $s ? 'hired' : $s ) ), esc_html( $l[ $s ] ?? $s ) );
			if ( get_post_meta( $id, '_qaf_e_unread', true ) ) {
				echo ' <span class="qaf-app-dot" title="غير مقروءة"></span>';
			}
			break;
	}
}
add_action( 'manage_qaf_entry_posts_custom_column', 'qaf_entry_column', 10, 2 );

/**
 * Meta boxes.
 */
function qaf_entry_boxes() {
	add_meta_box( 'qaf-entry', 'تفاصيل الرسالة', 'qaf_entry_render', 'qaf_entry', 'normal', 'high' );
	remove_meta_box( 'submitdiv', 'qaf_entry', 'side' );
	add_meta_box( 'qaf-entry-status', 'الحالة', 'qaf_entry_render_status', 'qaf_entry', 'side', 'high' );
}
add_action( 'add_meta_boxes', 'qaf_entry_boxes' );

/**
 * Render details + mark read.
 *
 * @param WP_Post $post Entry.
 */
function qaf_entry_render( $post ) {
	delete_post_meta( $post->ID, '_qaf_e_unread' );
	$form   = (string) get_post_meta( $post->ID, '_qaf_e_form', true );
	$def    = qaf_forms()[ $form ] ?? null;
	$values = (array) get_post_meta( $post->ID, '_qaf_e_values', true );

	echo '<table class="widefat striped qaf-app-table"><tbody>';
	printf( '<tr><th>النموذج</th><td>%s</td></tr>', esc_html( $def['label'] ?? $form ) );
	$tracking = (string) get_post_meta( $post->ID, '_qaf_e_tracking', true );
	if ( $tracking ) {
		printf( '<tr><th>رقم المتابعة</th><td><code>%s</code></td></tr>', esc_html( $tracking ) );
	}
	if ( $def ) {
		foreach ( $def['fields'] as $key => $field ) {
			if ( 'file' === $field['type'] ) {
				continue;
			}
			$display = qaf_form_display_value( $field, $values[ $key ] ?? '' );
			printf( '<tr><th>%s</th><td>%s</td></tr>', esc_html( wp_strip_all_tags( (string) qaf_opt( $field['label'] ) ) ), 'textarea' === $field['type'] ? nl2br( esc_html( $display ) ) : esc_html( $display ) );
		}
	}
	printf( '<tr><th>اللغة</th><td>%s</td></tr><tr><th>التاريخ</th><td>%s</td></tr>', esc_html( strtoupper( (string) get_post_meta( $post->ID, '_qaf_e_lang', true ) ) ), esc_html( get_the_date( 'Y-m-d H:i', $post ) ) );
	echo '</tbody></table>';

	if ( get_post_meta( $post->ID, '_qaf_e_file', true ) ) {
		printf( '<p><a class="button" href="%s">تحميل المرفق</a> <span class="description">%s</span></p>', esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=qaf_entry_file&id=' . $post->ID ), 'qaf_entry_file_' . $post->ID ) ), esc_html( (string) get_post_meta( $post->ID, '_qaf_e_file_name', true ) ) );
	}
	$email = $values['email'] ?? '';
	if ( is_email( $email ) ) {
		printf( '<p><a class="button button-primary" href="mailto:%s?subject=%s">الرد على المرسل</a></p>', esc_attr( $email ), rawurlencode( ( $tracking ? $tracking . ' — ' : '' ) . ( $values['subject'] ?? '' ) ) );
	}
}

/**
 * Status box with internal note.
 *
 * @param WP_Post $post Entry.
 */
function qaf_entry_render_status( $post ) {
	wp_nonce_field( 'qaf_entry_status', 'qaf_entry_status_nonce' );
	$current = (string) get_post_meta( $post->ID, '_qaf_e_status', true );
	echo '<select name="qaf_entry_status" style="width:100%">';
	foreach ( qaf_entry_status_labels() as $k => $l ) {
		printf( '<option value="%s"%s>%s</option>', esc_attr( $k ), selected( $current, $k, false ), esc_html( $l ) );
	}
	echo '</select>';
	printf( '<p><label>ملاحظة داخلية<textarea name="qaf_entry_note" rows="4" style="width:100%%">%s</textarea></label></p>', esc_textarea( (string) get_post_meta( $post->ID, '_qaf_e_note', true ) ) );
	echo '<p class="description">الحالة تظهر للمرسل عند الاستعلام برقم المتابعة. الملاحظة لا تظهر له.</p>';
	echo '<p><button class="button button-primary" type="submit" style="width:100%">حفظ</button></p>';
	printf( '<p><a style="color:#b32d2e" href="%s">حذف نهائي (مع المرفق)</a></p>', esc_url( get_delete_post_link( $post->ID, '', true ) ) );
}

/**
 * Save status.
 *
 * @param int $post_id Entry.
 */
function qaf_entry_save( $post_id ) {
	if ( ! isset( $_POST['qaf_entry_status_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['qaf_entry_status_nonce'] ) ), 'qaf_entry_status' ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$status = isset( $_POST['qaf_entry_status'] ) ? sanitize_key( wp_unslash( $_POST['qaf_entry_status'] ) ) : 'new';
	if ( in_array( $status, QAF_ENTRY_STATUSES, true ) ) {
		update_post_meta( $post_id, '_qaf_e_status', $status );
	}
	update_post_meta( $post_id, '_qaf_e_note', isset( $_POST['qaf_entry_note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['qaf_entry_note'] ) ) : '' );
}
add_action( 'save_post_qaf_entry', 'qaf_entry_save' );

/**
 * Keep entries private.
 *
 * @param array $data Data.
 * @return array
 */
function qaf_entry_force_private( $data ) {
	if ( 'qaf_entry' === $data['post_type'] && 'trash' !== $data['post_status'] ) {
		$data['post_status'] = 'private';
	}
	return $data;
}
add_filter( 'wp_insert_post_data', 'qaf_entry_force_private' );

/**
 * Secure attachment download.
 */
function qaf_entry_file() {
	$id = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;
	if ( ! $id || 'qaf_entry' !== get_post_type( $id ) || ! current_user_can( 'edit_post', $id ) ) {
		wp_die( 'غير مسموح', 403 );
	}
	check_admin_referer( 'qaf_entry_file_' . $id );
	$file = basename( (string) get_post_meta( $id, '_qaf_e_file', true ) );
	$dir  = qaf_attach_dir();
	$path = $dir . $file;
	if ( ! $file || ! $dir || ! file_exists( $path ) || 0 !== strpos( realpath( $path ), realpath( $dir ) ) ) {
		wp_die( 'الملف غير موجود', 404 );
	}
	$types = array( 'jpg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp', 'pdf' => 'application/pdf' );
	$ext   = strtolower( pathinfo( $file, PATHINFO_EXTENSION ) );
	nocache_headers();
	header( 'Content-Type: ' . ( $types[ $ext ] ?? 'application/octet-stream' ) );
	header( 'Content-Disposition: attachment; filename="entry-' . $id . '.' . $ext . '"' );
	header( 'Content-Length: ' . filesize( $path ) );
	header( 'X-Content-Type-Options: nosniff' );
	readfile( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	exit;
}
add_action( 'admin_post_qaf_entry_file', 'qaf_entry_file' );

/**
 * Unread badge on the top-level menu.
 */
function qaf_entry_badge() {
	global $menu;
	$unread = (int) ( new WP_Query( array( 'post_type' => 'qaf_entry', 'post_status' => 'private', 'meta_key' => '_qaf_e_unread', 'fields' => 'ids', 'posts_per_page' => 1 ) ) )->found_posts; // phpcs:ignore WordPress.DB.SlowDBQuery
	if ( ! $unread ) {
		return;
	}
	foreach ( $menu as &$item ) {
		if ( isset( $item[2] ) && 'edit.php?post_type=qaf_entry' === $item[2] ) {
			$item[0] .= ' <span class="awaiting-mod"><span class="pending-count">' . absint( $unread ) . '</span></span>';
		}
	}
}
add_action( 'admin_menu', 'qaf_entry_badge', 99 );

/**
 * Filters + export.
 *
 * @param string $post_type Post type.
 */
function qaf_entry_filters( $post_type ) {
	if ( 'qaf_entry' !== $post_type ) {
		return;
	}
	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	$form   = isset( $_GET['qaf_form'] ) ? sanitize_key( wp_unslash( $_GET['qaf_form'] ) ) : '';
	$status = isset( $_GET['qaf_status'] ) ? sanitize_key( wp_unslash( $_GET['qaf_status'] ) ) : '';
	// phpcs:enable
	echo '<select name="qaf_form"><option value="">كل النماذج</option>';
	foreach ( qaf_forms() as $k => $f ) {
		printf( '<option value="%s"%s>%s</option>', esc_attr( $k ), selected( $form, $k, false ), esc_html( $f['label'] ) );
	}
	echo '</select><select name="qaf_status"><option value="">كل الحالات</option>';
	foreach ( qaf_entry_status_labels() as $k => $l ) {
		printf( '<option value="%s"%s>%s</option>', esc_attr( $k ), selected( $status, $k, false ), esc_html( $l ) );
	}
	echo '</select>';
	printf( '<a class="button" href="%s">تصدير CSV</a>', esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'qaf_entry_export', 'qaf_form' => $form, 'qaf_status' => $status ), admin_url( 'admin-post.php' ) ), 'qaf_entry_export' ) ) );
}
add_action( 'restrict_manage_posts', 'qaf_entry_filters' );

/**
 * Meta query from filters.
 *
 * @param string $form   Form.
 * @param string $status Status.
 * @return array
 */
function qaf_entry_meta_query( $form, $status ) {
	$mq = array();
	if ( isset( qaf_forms()[ $form ] ) ) {
		$mq[] = array( 'key' => '_qaf_e_form', 'value' => $form );
	}
	if ( in_array( $status, QAF_ENTRY_STATUSES, true ) ) {
		$mq[] = array( 'key' => '_qaf_e_status', 'value' => $status );
	}
	return $mq;
}

/**
 * Apply filters.
 *
 * @param WP_Query $q Query.
 */
function qaf_entry_filter_query( $q ) {
	if ( ! is_admin() || ! $q->is_main_query() || 'qaf_entry' !== $q->get( 'post_type' ) ) {
		return;
	}
	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	$mq = qaf_entry_meta_query( isset( $_GET['qaf_form'] ) ? sanitize_key( wp_unslash( $_GET['qaf_form'] ) ) : '', isset( $_GET['qaf_status'] ) ? sanitize_key( wp_unslash( $_GET['qaf_status'] ) ) : '' );
	// phpcs:enable
	if ( $mq ) {
		$q->set( 'meta_query', $mq ); // phpcs:ignore WordPress.DB.SlowDBQuery
	}
}
add_action( 'pre_get_posts', 'qaf_entry_filter_query' );

/**
 * CSV export (formula-safe).
 */
function qaf_entry_export() {
	if ( ! current_user_can( 'edit_others_posts' ) ) {
		wp_die( 'غير مسموح', 403 );
	}
	check_admin_referer( 'qaf_entry_export' );
	$form   = isset( $_GET['qaf_form'] ) ? sanitize_key( wp_unslash( $_GET['qaf_form'] ) ) : '';
	$status = isset( $_GET['qaf_status'] ) ? sanitize_key( wp_unslash( $_GET['qaf_status'] ) ) : '';
	$ids    = get_posts( array( 'post_type' => 'qaf_entry', 'post_status' => 'private', 'posts_per_page' => -1, 'fields' => 'ids', 'meta_query' => qaf_entry_meta_query( $form, $status ) ) ); // phpcs:ignore WordPress.DB.SlowDBQuery

	$keys = array();
	foreach ( qaf_forms() as $fid => $def ) {
		if ( $form && $fid !== $form ) {
			continue;
		}
		foreach ( $def['fields'] as $k => $f ) {
			if ( 'file' !== $f['type'] ) {
				$keys[ $k ] = $f;
			}
		}
	}

	nocache_headers();
	header( 'Content-Type: text/csv; charset=UTF-8' );
	header( 'Content-Disposition: attachment; filename="entries-' . gmdate( 'Y-m-d' ) . '.csv"' );
	$out = fopen( 'php://output', 'w' ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	fwrite( $out, "\xEF\xBB\xBF" ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	fputcsv( $out, array_merge( array( 'Date', 'Form', 'Tracking', 'Status' ), array_keys( $keys ) ) );
	$labels = qaf_entry_status_labels();
	foreach ( $ids as $id ) {
		$values = (array) get_post_meta( $id, '_qaf_e_values', true );
		$st     = (string) get_post_meta( $id, '_qaf_e_status', true );
		$row    = array( get_the_date( 'Y-m-d H:i', $id ), get_post_meta( $id, '_qaf_e_form', true ), get_post_meta( $id, '_qaf_e_tracking', true ), $labels[ $st ] ?? $st );
		foreach ( $keys as $k => $f ) {
			$row[] = qaf_form_display_value( $f, $values[ $k ] ?? '' );
		}
		fputcsv( $out, array_map( 'qaf_csv_safe', $row ) );
	}
	fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	exit;
}
add_action( 'admin_post_qaf_entry_export', 'qaf_entry_export' );

/**
 * Row actions cleanup.
 *
 * @param array   $actions Actions.
 * @param WP_Post $post    Post.
 * @return array
 */
function qaf_entry_row_actions( $actions, $post ) {
	if ( 'qaf_entry' === $post->post_type ) {
		unset( $actions['inline hide-if-no-js'], $actions['view'] );
	}
	return $actions;
}
add_filter( 'post_row_actions', 'qaf_entry_row_actions', 10, 2 );
