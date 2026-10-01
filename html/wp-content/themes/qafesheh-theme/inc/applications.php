<?php
/**
 * Job applications: private storage, secure CV uploads, notifications.
 *
 * Security model
 * - Nonce + honeypot + minimum fill time + per-IP rate limit.
 * - CV: PDF / DOC / DOCX only, real MIME sniffed with finfo, size capped from options,
 *   random file name, stored OUTSIDE the media library in uploads/qaf-private/cv/
 *   (deny-all .htaccess + index.php + web.config). Downloaded only through an admin
 *   endpoint that checks capability and nonce.
 * - The recipient address comes from options only — never from the request.
 * - Job ID must be a published job with applications open, or 0 (open application).
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

const QAF_APP_STATUSES = array( 'new', 'reviewed', 'shortlisted', 'rejected', 'hired' );

/**
 * Private CPT for applications.
 */
function qaf_register_applications() {
	register_post_type(
		'qaf_application',
		array(
			'labels'          => array(
				'name'          => 'طلبات التوظيف',
				'singular_name' => 'طلب توظيف',
				'menu_name'     => 'الطلبات',
				'all_items'     => 'الطلبات',
				'edit_item'     => 'تفاصيل الطلب',
				'search_items'  => 'بحث في الطلبات',
				'not_found'     => 'لا توجد طلبات بعد',
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => 'edit.php?post_type=qaf_job',
			'show_in_rest'    => false,
			'capability_type' => 'post',
			'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'    => true,
			'supports'        => array( 'title' ),
		)
	);
}
add_action( 'init', 'qaf_register_applications' );

/**
 * Private upload directory (created with deny-all guards).
 *
 * @return string Absolute path with trailing slash, or '' on failure.
 */
function qaf_cv_dir() {
	$uploads = wp_upload_dir( null, false );
	$dir     = trailingslashit( $uploads['basedir'] ) . 'qaf-private/cv/';
	if ( ! wp_mkdir_p( $dir ) ) {
		return '';
	}
	$guards = array(
		dirname( $dir ) . '/.htaccess' => "Require all denied\nDeny from all\n",
		$dir . '.htaccess'             => "Require all denied\nDeny from all\n",
		dirname( $dir ) . '/index.php' => "<?php\n// Silence.\n",
		$dir . 'index.php'             => "<?php\n// Silence.\n",
		$dir . 'web.config'            => '<?xml version="1.0"?><configuration><system.webServer><authorization><deny users="*" /></authorization></system.webServer></configuration>',
	);
	foreach ( $guards as $file => $content ) {
		if ( ! file_exists( $file ) ) {
			file_put_contents( $file, $content ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		}
	}
	return $dir;
}

/**
 * Client IP (REMOTE_ADDR only — proxies' headers are spoofable).
 *
 * @return string
 */
function qaf_client_ip() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	return filter_var( $ip, FILTER_VALIDATE_IP ) ? $ip : '0.0.0.0';
}

/**
 * Handle a submission (AJAX and classic POST share this).
 *
 * @return array{ok:bool,message:string,errors:array}
 */
function qaf_application_process() {
	$fail = static function ( $message, $errors = array() ) {
		return array( 'ok' => false, 'message' => $message, 'errors' => $errors );
	};
	$generic = (string) qaf_opt_i18n( 'str_f_error' );

	// phpcs:disable WordPress.Security.NonceVerification.Missing -- verified right below.
	if ( ! isset( $_POST['qaf_apply_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['qaf_apply_nonce'] ) ), 'qaf_apply' ) ) {
		return $fail( $generic );
	}
	// phpcs:enable

	/* Honeypot + time trap: silently "succeed" for bots. */
	$hp   = isset( $_POST['qaf_website'] ) ? trim( (string) wp_unslash( $_POST['qaf_website'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	$time = isset( $_POST['qaf_t'] ) ? absint( $_POST['qaf_t'] ) : 0;
	if ( '' !== $hp || ! $time || ( time() - $time ) < 3 ) {
		return array( 'ok' => true, 'message' => (string) qaf_opt_i18n( 'apply_success' ), 'errors' => array() );
	}

	/* Human check (QB Shield / Turnstile / reCAPTCHA). */
	if ( function_exists( 'qb_captcha_verify' ) ) {
		$human = qb_captcha_verify( 'apply' );
		if ( is_wp_error( $human ) ) {
			return array( 'ok' => false, 'message' => $human->get_error_message(), 'errors' => array() );
		}
	}

	/* Rate limit: 5 submissions per IP per hour. */
	$key   = 'qaf_apply_' . md5( qaf_client_ip() );
	$count = (int) get_transient( $key );
	if ( $count >= 5 ) {
		return $fail( (string) qaf_opt_i18n( 'str_f_limit' ) );
	}

	$name    = isset( $_POST['qaf_name'] ) ? sanitize_text_field( wp_unslash( $_POST['qaf_name'] ) ) : '';
	$phone   = isset( $_POST['qaf_phone'] ) ? preg_replace( '/[^\d+\-\s()]/', '', sanitize_text_field( wp_unslash( $_POST['qaf_phone'] ) ) ) : '';
	$email   = isset( $_POST['qaf_email'] ) ? sanitize_email( wp_unslash( $_POST['qaf_email'] ) ) : '';
	$message = isset( $_POST['qaf_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['qaf_message'] ) ) : '';
	$job_id  = isset( $_POST['qaf_job'] ) ? absint( $_POST['qaf_job'] ) : 0;
	$dept    = isset( $_POST['qaf_dept'] ) ? absint( $_POST['qaf_dept'] ) : 0;
	$consent = ! empty( $_POST['qaf_consent'] );
	$lang    = qaf_current_lang();

	$errors   = array();
	$required = (string) qaf_opt_i18n( 'str_f_required' );
	if ( mb_strlen( $name ) < 2 ) {
		$errors['qaf_name'] = $required;
	}
	if ( strlen( preg_replace( '/\D/', '', $phone ) ) < 7 ) {
		$errors['qaf_phone'] = $required;
	}
	if ( ! is_email( $email ) ) {
		$errors['qaf_email'] = (string) qaf_opt_i18n( 'str_f_bad_email' );
	}
	if ( ! $consent ) {
		$errors['qaf_consent'] = $required;
	}
	$name    = mb_substr( $name, 0, 120 );
	$message = mb_substr( $message, 0, 3000 );

	/* Job must exist, be published and open. */
	if ( $job_id ) {
		$job = get_post( $job_id );
		if ( ! $job || 'qaf_job' !== $job->post_type || 'publish' !== $job->post_status || qaf_meta( 'closed', $job_id ) ) {
			return $fail( $generic );
		}
	}
	if ( $dept && ! term_exists( $dept, 'qaf_job_dept' ) ) {
		$dept = 0;
	}

	/* File. */
	$max_mb  = max( 1, min( 10, absint( qaf_opt( 'apply_max_mb' ) ) ) );
	$bad     = str_replace( '%s', (string) $max_mb, (string) qaf_opt_i18n( 'str_f_bad_file' ) );
	$file    = $_FILES['qaf_cv'] ?? null; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	$cv_info = null;

	if ( ! $file || ! isset( $file['error'] ) || UPLOAD_ERR_NO_FILE === (int) $file['error'] ) {
		$errors['qaf_cv'] = $required;
	} elseif ( UPLOAD_ERR_OK !== (int) $file['error'] || ! is_uploaded_file( $file['tmp_name'] ) || (int) $file['size'] > $max_mb * MB_IN_BYTES || (int) $file['size'] < 100 ) {
		$errors['qaf_cv'] = $bad;
	} else {
		$allowed = array(
			'pdf'  => 'application/pdf',
			'doc'  => 'application/msword',
			'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		);
		$ext   = strtolower( pathinfo( (string) $file['name'], PATHINFO_EXTENSION ) );
		$check = wp_check_filetype_and_ext( $file['tmp_name'], (string) $file['name'], $allowed );
		$real  = '';
		if ( function_exists( 'finfo_open' ) ) {
			$finfo = finfo_open( FILEINFO_MIME_TYPE );
			$real  = (string) finfo_file( $finfo, $file['tmp_name'] );
			finfo_close( $finfo );
		}
		$mime_ok = in_array( $real, array( 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip', 'application/octet-stream', 'application/x-ole-storage', 'application/CDFV2' ), true );
		$head    = (string) file_get_contents( $file['tmp_name'], false, null, 0, 8 ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		$magic   = ( 'pdf' === $ext && 0 === strpos( $head, '%PDF' ) )
			|| ( 'docx' === $ext && 0 === strpos( $head, "PK\x03\x04" ) )
			|| ( 'doc' === $ext && 0 === strpos( $head, "\xD0\xCF\x11\xE0" ) );

		if ( ! isset( $allowed[ $ext ] ) || empty( $check['ext'] ) || ! $mime_ok || ! $magic ) {
			$errors['qaf_cv'] = $bad;
		} else {
			$cv_info = array( 'ext' => $ext, 'name' => sanitize_file_name( (string) $file['name'] ), 'size' => (int) $file['size'] );
		}
	}

	if ( $errors ) {
		return $fail( $generic, $errors );
	}

	$dir = qaf_cv_dir();
	if ( ! $dir ) {
		return $fail( $generic );
	}
	$stored = wp_generate_password( 32, false, false ) . '.' . $cv_info['ext'];
	if ( ! move_uploaded_file( $file['tmp_name'], $dir . $stored ) ) {
		return $fail( $generic );
	}
	chmod( $dir . $stored, 0640 ); // phpcs:ignore WordPress.WP.AlternativeFunctions

	$job_title = $job_id ? get_the_title( $job_id ) : (string) qaf_opt_i18n( 'open_label' );
	$app_id    = wp_insert_post(
		array(
			'post_type'   => 'qaf_application',
			'post_status' => 'private',
			'post_title'  => $name . ' — ' . $job_title,
		),
		true
	);
	if ( is_wp_error( $app_id ) ) {
		wp_delete_file( $dir . $stored );
		return $fail( $generic );
	}

	$meta = array(
		'_qaf_app_name'    => $name,
		'_qaf_app_phone'   => $phone,
		'_qaf_app_email'   => $email,
		'_qaf_app_message' => $message,
		'_qaf_app_job'     => $job_id,
		'_qaf_app_dept'    => $dept,
		'_qaf_app_lang'    => $lang,
		'_qaf_app_status'  => 'new',
		'_qaf_app_unread'  => 1,
		'_qaf_app_cv'      => $stored,
		'_qaf_app_cv_name' => $cv_info['name'],
		'_qaf_app_cv_size' => $cv_info['size'],
		'_qaf_app_ip'      => wp_privacy_anonymize_ip( qaf_client_ip() ),
	);
	foreach ( $meta as $k => $v ) {
		update_post_meta( $app_id, $k, $v );
	}

	set_transient( $key, $count + 1, HOUR_IN_SECONDS );
	qaf_application_notify( $app_id, $dir . $stored, $job_title );

	return array( 'ok' => true, 'message' => (string) qaf_opt_i18n( 'apply_success' ), 'errors' => array() );
}

/**
 * Emails: HR notification (+ optional CV attachment) and applicant confirmation.
 *
 * @param int    $app_id    Application.
 * @param string $cv_path   Stored CV path.
 * @param string $job_title Job title.
 */
function qaf_application_notify( $app_id, $cv_path, $job_title ) {
	$to = sanitize_email( (string) qaf_opt( 'apply_email' ) );
	if ( ! is_email( $to ) ) {
		$to = get_option( 'admin_email' );
	}
	$name  = (string) get_post_meta( $app_id, '_qaf_app_name', true );
	$email = (string) get_post_meta( $app_id, '_qaf_app_email', true );
	$phone = (string) get_post_meta( $app_id, '_qaf_app_phone', true );
	$msg   = (string) get_post_meta( $app_id, '_qaf_app_message', true );
	$dept  = absint( get_post_meta( $app_id, '_qaf_app_dept', true ) );
	$term  = $dept ? get_term( $dept, 'qaf_job_dept' ) : null;

	$lines = array(
		'طلب توظيف جديد / New job application',
		'',
		'الوظيفة / Role: ' . $job_title,
		'الاسم / Name: ' . $name,
		'الهاتف / Phone: ' . $phone,
		'الإيميل / Email: ' . $email,
	);
	if ( $term && ! is_wp_error( $term ) ) {
		$lines[] = 'القسم / Department: ' . $term->name;
	}
	$lines[] = '';
	$lines[] = $msg;
	$lines[] = '';
	$lines[] = 'لوحة التحكم / Dashboard: ' . admin_url( 'post.php?post=' . $app_id . '&action=edit' );

	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
	if ( is_email( $email ) ) {
		$headers[] = 'Reply-To: ' . str_replace( array( "\r", "\n" ), '', $name ) . ' <' . $email . '>';
	}
	$attachments = qaf_opt( 'apply_attach' ) && file_exists( $cv_path ) ? array( $cv_path ) : array();

	wp_mail( $to, '[' . wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) . '] ' . $job_title . ' — ' . $name, implode( "\n", $lines ), $headers, $attachments );

	if ( qaf_opt( 'apply_autoreply' ) && is_email( $email ) ) {
		$body = str_replace( array( '{name}', '{job}' ), array( $name, $job_title ), (string) qaf_opt_i18n( 'apply_reply_body' ) );
		wp_mail( $email, (string) qaf_opt_i18n( 'apply_reply_subject' ), $body, array( 'Content-Type: text/plain; charset=UTF-8' ) );
	}
}

/**
 * AJAX endpoint.
 */
function qaf_ajax_apply() {
	$result = qaf_application_process();
	if ( $result['ok'] ) {
		wp_send_json_success( array( 'message' => $result['message'] ) );
	}
	wp_send_json_error( array( 'message' => $result['message'], 'errors' => $result['errors'] ), 422 );
}
add_action( 'wp_ajax_qaf_apply', 'qaf_ajax_apply' );
add_action( 'wp_ajax_nopriv_qaf_apply', 'qaf_ajax_apply' );

/**
 * No-JS fallback via admin-post.php → redirect back with a status flag.
 */
function qaf_post_apply() {
	$result = qaf_application_process();
	$back   = wp_get_referer() ? wp_get_referer() : home_url( '/' );
	$back   = remove_query_arg( array( 'qaf-applied', 'qaf-apply-error' ), $back );
	wp_safe_redirect( add_query_arg( $result['ok'] ? 'qaf-applied' : 'qaf-apply-error', '1', $back ) . '#apply' );
	exit;
}
add_action( 'admin_post_qaf_apply', 'qaf_post_apply' );
add_action( 'admin_post_nopriv_qaf_apply', 'qaf_post_apply' );

/**
 * Delete the private CV with its application.
 *
 * @param int $post_id Post.
 */
function qaf_application_delete_file( $post_id ) {
	if ( 'qaf_application' !== get_post_type( $post_id ) ) {
		return;
	}
	$file = basename( (string) get_post_meta( $post_id, '_qaf_app_cv', true ) );
	$dir  = qaf_cv_dir();
	if ( $file && $dir && file_exists( $dir . $file ) ) {
		wp_delete_file( $dir . $file );
	}
}
add_action( 'before_delete_post', 'qaf_application_delete_file' );

/**
 * Daily retention purge (option in days, 0 = keep).
 */
function qaf_applications_retention() {
	$days = absint( qaf_opt( 'apply_retention' ) );
	if ( ! $days ) {
		return;
	}
	$old = get_posts(
		array(
			'post_type'      => 'qaf_application',
			'post_status'    => 'any',
			'posts_per_page' => 200,
			'fields'         => 'ids',
			'date_query'     => array( array( 'before' => $days . ' days ago' ) ),
		)
	);
	foreach ( $old as $id ) {
		wp_delete_post( $id, true );
	}
}
add_action( 'qaf_daily', 'qaf_applications_retention' );

/**
 * Schedule the daily event once.
 */
function qaf_schedule_daily() {
	if ( ! wp_next_scheduled( 'qaf_daily' ) ) {
		wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'qaf_daily' );
	}
}
add_action( 'init', 'qaf_schedule_daily' );

/**
 * Personal-data eraser/exporter hooks (Tools → Erase personal data).
 *
 * @param array $erasers Erasers.
 * @return array
 */
function qaf_register_eraser( $erasers ) {
	$erasers['qaf-applications'] = array(
		'eraser_friendly_name' => 'طلبات التوظيف',
		'callback'             => static function ( $email ) {
			$ids = get_posts( array( 'post_type' => 'qaf_application', 'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids', 'meta_key' => '_qaf_app_email', 'meta_value' => sanitize_email( $email ) ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
			foreach ( $ids as $id ) {
				wp_delete_post( $id, true );
			}
			return array( 'items_removed' => count( $ids ), 'items_retained' => false, 'messages' => array(), 'done' => true );
		},
	);
	return $erasers;
}
add_filter( 'wp_privacy_personal_data_erasers', 'qaf_register_eraser' );
