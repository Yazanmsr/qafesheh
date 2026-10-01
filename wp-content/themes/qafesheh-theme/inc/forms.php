<?php
/**
 * Site forms: contact + complaints & suggestions.
 *
 * One engine, schema-defined forms, stored as private «qaf_entry» posts ("Form Entries").
 * Same protections as the application system: nonce, honeypot, time trap, per-IP rate limit,
 * server-side recipients only, optional attachment in the private uploads folder.
 * Complaints get a tracking number the visitor can look up with their email.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

const QAF_ENTRY_STATUSES = array( 'new', 'progress', 'resolved', 'closed' );

/**
 * Form definitions: form => [ label, recipient option, fields ].
 * Field: type (text|email|tel|select|textarea|choice|file), required, label option key.
 *
 * @return array
 */
function qaf_forms() {
	return array(
		'contact'   => array(
			'label'     => 'تواصل معنا',
			'recipient' => 'form_contact_email',
			'fields'    => array(
				'name'    => array( 'type' => 'text', 'required' => true, 'label' => 'str_f_name', 'max' => 120 ),
				'phone'   => array( 'type' => 'tel', 'required' => true, 'label' => 'str_f_phone', 'max' => 40 ),
				'email'   => array( 'type' => 'email', 'required' => true, 'label' => 'str_f_email', 'max' => 190 ),
				'company' => array( 'type' => 'select', 'required' => false, 'label' => 'str_f_target', 'source' => 'companies_group' ),
				'subject' => array( 'type' => 'text', 'required' => true, 'label' => 'str_f_subject', 'max' => 190 ),
				'message' => array( 'type' => 'textarea', 'required' => true, 'label' => 'str_f_message', 'max' => 4000 ),
			),
		),
		'complaint' => array(
			'label'     => 'شكاوى واقتراحات',
			'recipient' => 'form_complaint_email',
			'fields'    => array(
				'kind'    => array( 'type' => 'choice', 'required' => true, 'label' => 'str_f_kind', 'options' => array( 'complaint' => 'str_k_complaint', 'suggestion' => 'str_k_suggestion', 'praise' => 'str_k_praise' ) ),
				'name'    => array( 'type' => 'text', 'required' => true, 'label' => 'str_f_name', 'max' => 120 ),
				'phone'   => array( 'type' => 'tel', 'required' => true, 'label' => 'str_f_phone', 'max' => 40 ),
				'email'   => array( 'type' => 'email', 'required' => true, 'label' => 'str_f_email', 'max' => 190 ),
				'company' => array( 'type' => 'select', 'required' => false, 'label' => 'str_f_company', 'source' => 'companies' ),
				'branch'  => array( 'type' => 'select', 'required' => false, 'label' => 'str_f_branch', 'source' => 'branches' ),
				'order'   => array( 'type' => 'text', 'required' => false, 'label' => 'str_f_order', 'max' => 60 ),
				'subject' => array( 'type' => 'text', 'required' => true, 'label' => 'str_f_subject', 'max' => 190 ),
				'message' => array( 'type' => 'textarea', 'required' => true, 'label' => 'str_f_details', 'max' => 4000 ),
				'file'    => array( 'type' => 'file', 'required' => false, 'label' => 'str_f_attach' ),
			),
		),
	);
}

/**
 * Private CPT.
 */
function qaf_register_entries() {
	register_post_type(
		'qaf_entry',
		array(
			'labels'          => array(
				'name'          => 'رسائل الموقع',
				'singular_name' => 'رسالة',
				'menu_name'     => 'الرسائل',
				'all_items'     => 'كل الرسائل',
				'edit_item'     => 'تفاصيل الرسالة',
				'search_items'  => 'بحث في الرسائل',
				'not_found'     => 'لا توجد رسائل بعد',
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_rest'    => false,
			'menu_icon'       => 'dashicons-email-alt',
			'menu_position'   => 26,
			'capability_type' => 'post',
			'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'    => true,
			'supports'        => array( 'title' ),
		)
	);
}
add_action( 'init', 'qaf_register_entries' );

/**
 * Select options by source (server truth — submitted values are validated against these IDs).
 *
 * @param string $source Source.
 * @return array id => [ label, extra data ]
 */
function qaf_form_options( $source ) {
	$out = array();
	if ( 'companies' === $source || 'companies_group' === $source ) {
		if ( 'companies_group' === $source ) {
			$out['group'] = array( (string) qaf_opt_i18n( 'brand_name' ), '' );
		}
		foreach ( get_posts( array( 'post_type' => 'qaf_company', 'posts_per_page' => 20, 'orderby' => array( 'menu_order' => 'ASC' ), 'suppress_filters' => false ) ) as $p ) {
			$terms           = get_the_terms( $p->ID, 'qaf_brand' );
			$out[ $p->ID ] = array( get_the_title( $p ), $terms && ! is_wp_error( $terms ) ? qaf_brand_base_slug( $terms[0]->slug ) : '' );
		}
		if ( 'companies_group' === $source ) {
			$out['wholesale'] = array( (string) qaf_opt_i18n( 'str_f_wholesale' ), '' );
		}
	} elseif ( 'branches' === $source ) {
		foreach ( get_posts( array( 'post_type' => 'qaf_branch', 'posts_per_page' => 50, 'orderby' => array( 'menu_order' => 'ASC' ), 'suppress_filters' => false ) ) as $p ) {
			$terms           = get_the_terms( $p->ID, 'qaf_brand' );
			$slugs           = $terms && ! is_wp_error( $terms ) ? array_map( static fn( $t ) => qaf_brand_base_slug( $t->slug ), $terms ) : array();
			$out[ $p->ID ] = array( get_the_title( $p ), implode( ' ', $slugs ) );
		}
	}
	return $out;
}

/**
 * Tracking number: QG-YYMM-XXXXX (unambiguous characters).
 *
 * @return string
 */
function qaf_tracking_number() {
	$alphabet = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
	do {
		$code = '';
		for ( $i = 0; $i < 5; $i++ ) {
			$code .= $alphabet[ random_int( 0, strlen( $alphabet ) - 1 ) ];
		}
		$number = 'QG-' . wp_date( 'ym' ) . '-' . $code;
		$exists = get_posts( array( 'post_type' => 'qaf_entry', 'post_status' => 'any', 'meta_key' => '_qaf_e_tracking', 'meta_value' => $number, 'fields' => 'ids', 'posts_per_page' => 1 ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
	} while ( $exists );
	return $number;
}

/**
 * Private attachments folder.
 *
 * @return string
 */
function qaf_attach_dir() {
	$base = qaf_cv_dir();
	if ( ! $base ) {
		return '';
	}
	$dir = dirname( $base ) . '/attachments/';
	if ( ! wp_mkdir_p( $dir ) ) {
		return '';
	}
	foreach ( array( '.htaccess' => "Require all denied\nDeny from all\n", 'index.php' => "<?php\n// Silence.\n" ) as $f => $c ) {
		if ( ! file_exists( $dir . $f ) ) {
			file_put_contents( $dir . $f, $c ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		}
	}
	return $dir;
}

/**
 * Validate + store one submission.
 *
 * @return array{ok:bool,message:string,errors:array,tracking:string}
 */
function qaf_form_process() {
	$generic = (string) qaf_opt_i18n( 'str_f_error' );
	$fail    = static fn( $m, $e = array() ) => array( 'ok' => false, 'message' => $m, 'errors' => $e, 'tracking' => '' );

	// phpcs:disable WordPress.Security.NonceVerification.Missing -- verified next.
	$form_id = isset( $_POST['qaf_form'] ) ? sanitize_key( wp_unslash( $_POST['qaf_form'] ) ) : '';
	$forms   = qaf_forms();
	if ( ! isset( $forms[ $form_id ] ) || ! isset( $_POST['qaf_form_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['qaf_form_nonce'] ) ), 'qaf_form_' . $form_id ) ) {
		return $fail( $generic );
	}
	$hp   = isset( $_POST['qaf_website'] ) ? trim( (string) wp_unslash( $_POST['qaf_website'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	$time = isset( $_POST['qaf_t'] ) ? absint( $_POST['qaf_t'] ) : 0;
	// phpcs:enable
	$success = (string) qaf_opt_i18n( 'complaint' === $form_id ? 'form_complaint_success' : 'form_contact_success' );
	if ( '' !== $hp || ! $time || ( time() - $time ) < 3 ) {
		return array( 'ok' => true, 'message' => $success, 'errors' => array(), 'tracking' => '' );
	}

	if ( function_exists( 'qb_captcha_verify' ) ) {
		$human = qb_captcha_verify( 'form_' . $form_id );
		if ( is_wp_error( $human ) ) {
			return $fail( $human->get_error_message() );
		}
	}

	$rate_key = 'qaf_form_' . md5( qaf_client_ip() );
	$count    = (int) get_transient( $rate_key );
	if ( $count >= 8 ) {
		return $fail( (string) qaf_opt_i18n( 'str_f_limit' ) );
	}

	$form     = $forms[ $form_id ];
	$required = (string) qaf_opt_i18n( 'str_f_required' );
	$values   = array();
	$errors   = array();
	$upload   = null;

	foreach ( $form['fields'] as $key => $field ) {
		$name = 'qaf_' . $key;
		// phpcs:disable WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput
		switch ( $field['type'] ) {
			case 'email':
				$v = isset( $_POST[ $name ] ) ? sanitize_email( wp_unslash( $_POST[ $name ] ) ) : '';
				if ( $v && ! is_email( $v ) ) {
					$errors[ $name ] = (string) qaf_opt_i18n( 'str_f_bad_email' );
				}
				break;
			case 'tel':
				$v = isset( $_POST[ $name ] ) ? preg_replace( '/[^\d+\-\s()]/', '', sanitize_text_field( wp_unslash( $_POST[ $name ] ) ) ) : '';
				if ( $v && strlen( preg_replace( '/\D/', '', $v ) ) < 7 ) {
					$errors[ $name ] = $required;
				}
				break;
			case 'textarea':
				$v = isset( $_POST[ $name ] ) ? sanitize_textarea_field( wp_unslash( $_POST[ $name ] ) ) : '';
				break;
			case 'select':
				$raw     = isset( $_POST[ $name ] ) ? sanitize_text_field( wp_unslash( $_POST[ $name ] ) ) : '';
				$options = qaf_form_options( $field['source'] );
				$v       = array_key_exists( $raw, $options ) || ( ctype_digit( $raw ) && array_key_exists( (int) $raw, $options ) ) ? $raw : '';
				break;
			case 'choice':
				$raw = isset( $_POST[ $name ] ) ? sanitize_key( wp_unslash( $_POST[ $name ] ) ) : '';
				$v   = array_key_exists( $raw, $field['options'] ) ? $raw : '';
				break;
			case 'file':
				$v = '';
				if ( ! empty( $_FILES[ $name ] ) && UPLOAD_ERR_NO_FILE !== (int) $_FILES[ $name ]['error'] ) {
					$upload = qaf_form_check_file( $_FILES[ $name ] );
					if ( is_wp_error( $upload ) ) {
						$errors[ $name ] = $upload->get_error_message();
						$upload          = null;
					}
				}
				break;
			default:
				$v = isset( $_POST[ $name ] ) ? sanitize_text_field( wp_unslash( $_POST[ $name ] ) ) : '';
		}
		// phpcs:enable
		if ( isset( $field['max'] ) && is_string( $v ) ) {
			$v = mb_substr( $v, 0, $field['max'] );
		}
		if ( ! empty( $field['required'] ) && '' === (string) $v && ! isset( $errors[ $name ] ) ) {
			$errors[ $name ] = $required;
		}
		$values[ $key ] = $v;
	}

	if ( $errors ) {
		return $fail( $generic, $errors );
	}

	$tracking = 'complaint' === $form_id ? qaf_tracking_number() : '';
	$title    = ( $tracking ? $tracking . ' — ' : '' ) . ( $values['subject'] ?? '' ) . ' — ' . ( $values['name'] ?? '' );
	$entry_id = wp_insert_post( array( 'post_type' => 'qaf_entry', 'post_status' => 'private', 'post_title' => $title ), true );
	if ( is_wp_error( $entry_id ) ) {
		return $fail( $generic );
	}

	if ( $upload ) {
		$dir = qaf_attach_dir();
		$fn  = wp_generate_password( 32, false, false ) . '.' . $upload['ext'];
		if ( $dir && move_uploaded_file( $upload['tmp'], $dir . $fn ) ) {
			update_post_meta( $entry_id, '_qaf_e_file', $fn );
			update_post_meta( $entry_id, '_qaf_e_file_name', $upload['name'] );
		}
	}

	update_post_meta( $entry_id, '_qaf_e_form', $form_id );
	update_post_meta( $entry_id, '_qaf_e_values', $values );
	update_post_meta( $entry_id, '_qaf_e_email', $values['email'] ?? '' );
	update_post_meta( $entry_id, '_qaf_e_status', 'new' );
	update_post_meta( $entry_id, '_qaf_e_unread', 1 );
	update_post_meta( $entry_id, '_qaf_e_lang', qaf_current_lang() );
	update_post_meta( $entry_id, '_qaf_e_ip', wp_privacy_anonymize_ip( qaf_client_ip() ) );
	if ( $tracking ) {
		update_post_meta( $entry_id, '_qaf_e_tracking', $tracking );
	}

	set_transient( $rate_key, $count + 1, HOUR_IN_SECONDS );
	qaf_form_notify( $entry_id, $form_id, $values, $tracking );

	if ( $tracking ) {
		$success = str_replace( '{tracking}', $tracking, $success );
	}
	return array( 'ok' => true, 'message' => $success, 'errors' => array(), 'tracking' => $tracking );
}

/**
 * Optional attachment check: images or PDF, 5 MB.
 *
 * @param array $file $_FILES item.
 * @return array|WP_Error
 */
function qaf_form_check_file( $file ) {
	$bad = new WP_Error( 'file', str_replace( '%s', '5', (string) qaf_opt_i18n( 'str_f_bad_attach' ) ) );
	if ( UPLOAD_ERR_OK !== (int) $file['error'] || ! is_uploaded_file( $file['tmp_name'] ) || (int) $file['size'] > 5 * MB_IN_BYTES || (int) $file['size'] < 100 ) {
		return $bad;
	}
	$allowed = array( 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp', 'pdf' => 'application/pdf' );
	$ext     = strtolower( pathinfo( (string) $file['name'], PATHINFO_EXTENSION ) );
	$check   = wp_check_filetype_and_ext( $file['tmp_name'], (string) $file['name'], $allowed );
	$head    = (string) file_get_contents( $file['tmp_name'], false, null, 0, 12 ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	$magic   = ( 'pdf' === $ext && 0 === strpos( $head, '%PDF' ) )
		|| ( in_array( $ext, array( 'jpg', 'jpeg' ), true ) && 0 === strpos( $head, "\xFF\xD8\xFF" ) )
		|| ( 'png' === $ext && 0 === strpos( $head, "\x89PNG" ) )
		|| ( 'webp' === $ext && 'WEBP' === substr( $head, 8, 4 ) );
	if ( ! isset( $allowed[ $ext ] ) || empty( $check['ext'] ) || ! $magic ) {
		return $bad;
	}
	if ( 'pdf' !== $ext && ! @getimagesize( $file['tmp_name'] ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors
		return $bad;
	}
	return array( 'tmp' => $file['tmp_name'], 'ext' => 'jpeg' === $ext ? 'jpg' : $ext, 'name' => sanitize_file_name( (string) $file['name'] ) );
}

/**
 * Human value of a field (labels for selects/choices).
 *
 * @param array  $field Field.
 * @param string $value Stored value.
 * @return string
 */
function qaf_form_display_value( $field, $value ) {
	if ( '' === (string) $value ) {
		return '';
	}
	if ( 'select' === $field['type'] ) {
		$opts = qaf_form_options( $field['source'] );
		$key  = ctype_digit( (string) $value ) ? (int) $value : $value;
		return isset( $opts[ $key ] ) ? $opts[ $key ][0] : (string) $value;
	}
	if ( 'choice' === $field['type'] ) {
		return isset( $field['options'][ $value ] ) ? (string) qaf_opt_i18n( $field['options'][ $value ] ) : (string) $value;
	}
	return (string) $value;
}

/**
 * Notifications.
 *
 * @param int    $entry_id Entry.
 * @param string $form_id  Form.
 * @param array  $values   Values.
 * @param string $tracking Tracking.
 */
function qaf_form_notify( $entry_id, $form_id, $values, $tracking ) {
	$form = qaf_forms()[ $form_id ];
	$to   = array_filter( array_map( 'trim', explode( ',', (string) qaf_opt( $form['recipient'] ) ) ), 'is_email' );
	if ( ! $to ) {
		$to = array( get_option( 'admin_email' ) );
	}

	/* Route a copy to the chosen company's own inbox when enabled (email read from the post, never from the request). */
	$company = isset( $values['company'] ) && ctype_digit( (string) $values['company'] ) ? (int) $values['company'] : 0;
	if ( $company && qaf_opt( 'form_route_company' ) && 'qaf_company' === get_post_type( $company ) ) {
		$company_email = sanitize_email( qaf_meta( 'email', $company ) );
		if ( is_email( $company_email ) ) {
			$to[] = $company_email;
		}
	}

	$lines = array( $form['label'] . ( $tracking ? ' — ' . $tracking : '' ), '' );
	foreach ( $form['fields'] as $key => $field ) {
		if ( 'file' === $field['type'] ) {
			continue;
		}
		$display = qaf_form_display_value( $field, $values[ $key ] ?? '' );
		if ( '' !== $display ) {
			$lines[] = wp_strip_all_tags( (string) qaf_opt_i18n( $field['label'] ) ) . ': ' . $display;
		}
	}
	$lines[] = '';
	$lines[] = 'لوحة التحكم / Dashboard: ' . admin_url( 'post.php?post=' . $entry_id . '&action=edit' );

	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
	$email   = $values['email'] ?? '';
	if ( is_email( $email ) ) {
		$headers[] = 'Reply-To: ' . str_replace( array( "\r", "\n" ), '', (string) ( $values['name'] ?? '' ) ) . ' <' . $email . '>';
	}
	$subject = '[' . wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) . '] ' . $form['label'] . ( $tracking ? ' ' . $tracking : '' ) . ' — ' . ( $values['subject'] ?? '' );
	wp_mail( array_unique( $to ), $subject, implode( "\n", $lines ), $headers );

	if ( $tracking && is_email( $email ) ) {
		$body = str_replace( array( '{name}', '{tracking}' ), array( (string) ( $values['name'] ?? '' ), $tracking ), (string) qaf_opt_i18n( 'form_track_body' ) );
		wp_mail( $email, str_replace( '{tracking}', $tracking, (string) qaf_opt_i18n( 'form_track_subject' ) ), $body, array( 'Content-Type: text/plain; charset=UTF-8' ) );
	}
}

/**
 * AJAX submit.
 */
function qaf_ajax_form() {
	$r = qaf_form_process();
	if ( $r['ok'] ) {
		wp_send_json_success( array( 'message' => $r['message'], 'tracking' => $r['tracking'] ) );
	}
	wp_send_json_error( array( 'message' => $r['message'], 'errors' => $r['errors'] ), 422 );
}
add_action( 'wp_ajax_qaf_form', 'qaf_ajax_form' );
add_action( 'wp_ajax_nopriv_qaf_form', 'qaf_ajax_form' );

/**
 * No-JS fallback.
 */
function qaf_post_form() {
	$r    = qaf_form_process();
	$back = remove_query_arg( array( 'qaf-sent', 'qaf-form-error', 'qaf-tracking' ), wp_get_referer() ? wp_get_referer() : home_url( '/' ) );
	$args = $r['ok'] ? array( 'qaf-sent' => 1, 'qaf-tracking' => $r['tracking'] ) : array( 'qaf-form-error' => 1 );
	wp_safe_redirect( add_query_arg( array_filter( $args ), $back ) . '#qaf-form' );
	exit;
}
add_action( 'admin_post_qaf_form', 'qaf_post_form' );
add_action( 'admin_post_nopriv_qaf_form', 'qaf_post_form' );

/**
 * AJAX: tracking status lookup (tracking number + email must both match).
 */
function qaf_ajax_track() {
	check_ajax_referer( 'qaf_track', 'nonce' );
	$rate_key = 'qaf_track_' . md5( qaf_client_ip() );
	$count    = (int) get_transient( $rate_key );
	if ( $count >= 15 ) {
		wp_send_json_error( array( 'message' => (string) qaf_opt_i18n( 'str_f_limit' ) ), 429 );
	}
	set_transient( $rate_key, $count + 1, HOUR_IN_SECONDS );

	$number = isset( $_POST['tracking'] ) ? strtoupper( preg_replace( '/[^A-Za-z0-9\-]/', '', sanitize_text_field( wp_unslash( $_POST['tracking'] ) ) ) ) : '';
	$email  = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$none   = (string) qaf_opt_i18n( 'str_track_none' );
	if ( ! $number || ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => $none ), 404 );
	}
	$ids = get_posts( array( 'post_type' => 'qaf_entry', 'post_status' => 'private', 'meta_key' => '_qaf_e_tracking', 'meta_value' => $number, 'fields' => 'ids', 'posts_per_page' => 1 ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
	if ( ! $ids || strtolower( (string) get_post_meta( $ids[0], '_qaf_e_email', true ) ) !== strtolower( $email ) ) {
		wp_send_json_error( array( 'message' => $none ), 404 );
	}
	$status = (string) get_post_meta( $ids[0], '_qaf_e_status', true );
	$label  = (string) qaf_opt_i18n( 'str_status_' . ( in_array( $status, QAF_ENTRY_STATUSES, true ) ? $status : 'new' ) );
	wp_send_json_success(
		array(
			'message' => $number . ' — ' . $label,
			'status'  => $status,
			'date'    => get_the_date( '', $ids[0] ),
		)
	);
}
add_action( 'wp_ajax_qaf_track', 'qaf_ajax_track' );
add_action( 'wp_ajax_nopriv_qaf_track', 'qaf_ajax_track' );

/**
 * Remove private attachment with its entry.
 *
 * @param int $post_id Post.
 */
function qaf_entry_delete_file( $post_id ) {
	if ( 'qaf_entry' !== get_post_type( $post_id ) ) {
		return;
	}
	$file = basename( (string) get_post_meta( $post_id, '_qaf_e_file', true ) );
	$dir  = qaf_attach_dir();
	if ( $file && $dir && file_exists( $dir . $file ) ) {
		wp_delete_file( $dir . $file );
	}
}
add_action( 'before_delete_post', 'qaf_entry_delete_file' );

/**
 * Personal-data eraser for entries.
 *
 * @param array $erasers Erasers.
 * @return array
 */
function qaf_entries_eraser( $erasers ) {
	$erasers['qaf-entries'] = array(
		'eraser_friendly_name' => 'رسائل الموقع',
		'callback'             => static function ( $email ) {
			$ids = get_posts( array( 'post_type' => 'qaf_entry', 'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids', 'meta_key' => '_qaf_e_email', 'meta_value' => sanitize_email( $email ) ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
			foreach ( $ids as $id ) {
				wp_delete_post( $id, true );
			}
			return array( 'items_removed' => count( $ids ), 'items_retained' => false, 'messages' => array(), 'done' => true );
		},
	);
	return $erasers;
}
add_filter( 'wp_privacy_personal_data_erasers', 'qaf_entries_eraser' );
