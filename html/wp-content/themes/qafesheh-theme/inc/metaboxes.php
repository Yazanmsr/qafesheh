<?php
/**
 * Per-post fields: CPT details + per-page banner controls.
 * Schema-driven like the options: one array defines box, fields, saving and sanitising.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

/**
 * Meta schema: post type => [ title, fields ].
 * Keys are stored with a leading underscore (_qaf_…).
 *
 * @return array
 */
function qaf_meta_schema() {
	$banner = array(
		'banner_off'   => array( 'type' => 'switch', 'label' => 'إخفاء البنر' ),
		'banner_title' => array( 'type' => 'text', 'label' => 'عنوان مخصص للبنر' ),
		'banner_sub'   => array( 'type' => 'text', 'label' => 'سطر علوي (Eyebrow)' ),
		'banner_image' => array( 'type' => 'image', 'label' => 'صورة البنر', 'desc' => 'فارغ = الصورة البارزة ثم الافتراضية.' ),
	);

	return array(
		'qaf_company' => array(
			'title'  => 'بيانات العلامة',
			'fields' => array(
				'en_name'   => array( 'type' => 'text', 'label' => 'الاسم اللاتيني', 'desc' => 'مثال: QAFESHEH' ),
				'sector'    => array( 'type' => 'text', 'label' => 'القطاع (لاتيني)', 'desc' => 'يظهر تحت الاسم، مثل FUR & BISHT' ),
				'color'     => array( 'type' => 'color', 'label' => 'لون الهوية', 'desc' => 'الشريط العلوي والخط والسهم في بطاقتها.' ),
				'logo'      => array( 'type' => 'image', 'label' => 'شعار على خلفية داكنة' ),
				'logo_dark' => array( 'type' => 'image', 'label' => 'شعار على خلفية فاتحة' ),
				'specialty' => array( 'type' => 'text', 'label' => 'التخصص', 'desc' => 'افصل بـ ·' ),
				'markets'   => array( 'type' => 'text', 'label' => 'الأسواق' ),
				'points'    => array( 'type' => 'text', 'label' => 'نقاط البيع' ),
				'shop_url'  => array( 'type' => 'url', 'label' => 'رابط المتجر' ),
				'img_pos'   => array( 'type' => 'select', 'label' => 'تموضع الصورة بالبطاقة', 'options' => array( 'center' => 'وسط', 'left center' => 'يسار', 'right center' => 'يمين' ) ),
				'note_page' => array( 'type' => 'heading', 'label' => 'صفحة الشركة' ),
				'tagline'   => array( 'type' => 'textarea', 'label' => 'سطر الهيرو', 'desc' => 'يظهر تحت الاسم في رأس الصفحة.' ),
				'catalog'   => array( 'type' => 'url', 'label' => 'ملف الكتالوج (PDF)', 'desc' => 'ارفعه من الوسائط والصق الرابط. فارغ = يختفي الزر.' ),
				'intro_t'   => array( 'type' => 'textarea', 'label' => 'عنوان «من نحن»', 'desc' => 'سطر جديد = سطر جديد بالعنوان. نص الفقرات من محرر المحتوى.' ),
				'gallery'   => array( 'type' => 'gallery', 'label' => 'معرض الصور', 'desc' => '5 صور: الأولى الكبيرة المقوّسة ثم أربع صغيرة. اسحب لإعادة الترتيب.' ),
				'categories' => array(
					'type'   => 'repeater',
					'label'  => 'التصنيفات',
					'fields' => array(
						'name'  => array( 'type' => 'text', 'label' => 'الاسم' ),
						'image' => array( 'type' => 'image', 'label' => 'الصورة' ),
						'url'   => array( 'type' => 'url', 'label' => 'رابط (اختياري)' ),
					),
				),
				'quote_t'   => array( 'type' => 'text', 'label' => 'عنوان الاقتباس' ),
				'quote'     => array( 'type' => 'textarea', 'label' => 'نص الاقتباس' ),
				'email'     => array( 'type' => 'email', 'label' => 'إيميل الشركة' ),
				'phone'     => array( 'type' => 'text', 'label' => 'هاتف الشركة' ),
				'address'   => array( 'type' => 'textarea', 'label' => 'العناوين', 'desc' => 'كل عنوان بسطر.' ),
				'instagram' => array( 'type' => 'url', 'label' => 'Instagram' ),
				'facebook'  => array( 'type' => 'url', 'label' => 'Facebook' ),
				'youtube'   => array( 'type' => 'url', 'label' => 'YouTube' ),
			) + $banner,
		),
		'qaf_service' => array(
			'title'  => 'بيانات الخدمة',
			'fields' => array(
				'icon' => array(
					'type'    => 'select',
					'label'   => 'الأيقونة',
					'options' => array(
						'factory' => 'مصنع',
						'tailor'  => 'تفصيل',
						'truck'   => 'شحن',
						'globe'   => 'تصدير',
						'box'     => 'عناية',
						'star'    => 'علامة خاصة',
						'needle'  => 'تطريز',
						'pen'     => 'تصميم',
					),
				),
				'short'    => array( 'type' => 'textarea', 'label' => 'نص مختصر لبطاقة الرئيسية', 'desc' => 'فارغ = المقتطف.' ),
				'includes' => array( 'type' => 'textarea', 'label' => 'ما تشمله الخدمة', 'desc' => 'كل بند بسطر.' ),
				'gallery'  => array( 'type' => 'gallery', 'label' => 'صور داخل الصفحة', 'desc' => 'صورتان تظهران تحت البنود.' ),
				'steps'    => array( 'type' => 'textarea', 'label' => 'مراحل العمل', 'desc' => 'كل مرحلة بسطر (4 مراحل مثالياً).' ),
			) + $banner,
		),
		'qaf_branch'  => array(
			'title'  => 'بيانات الفرع',
			'fields' => array(
				'short'   => array( 'type' => 'text', 'label' => 'الاسم المختصر للقوائم', 'desc' => 'مثال: وسط البلد — شارع الملك طلال' ),
				'city'    => array( 'type' => 'text', 'label' => 'المدينة' ),
				'address' => array( 'type' => 'text', 'label' => 'العنوان' ),
				'phone'   => array( 'type' => 'text', 'label' => 'الهاتف' ),
				'hours'   => array( 'type' => 'text', 'label' => 'ساعات العمل', 'desc' => 'مثال: ٩:٠٠ — ٢١:٠٠' ),
				'lat'     => array( 'type' => 'text', 'label' => 'خط العرض (Latitude)' ),
				'lng'     => array( 'type' => 'text', 'label' => 'خط الطول (Longitude)' ),
				'map_url' => array( 'type' => 'url', 'label' => 'رابط خرائط جوجل' ),
				'factory' => array( 'type' => 'switch', 'label' => 'هذا مصنع لا معرض' ),
			),
		),
		'qaf_job'     => array(
			'title'  => 'بيانات الوظيفة',
			'fields' => array(
				'note_tax'   => array( 'type' => 'heading', 'label' => 'القسم والموقع ونوع الدوام تُختار من الصناديق الجانبية.' ),
				'experience' => array( 'type' => 'text', 'label' => 'الخبرة المطلوبة', 'desc' => 'مثال: سنتان فأكثر' ),
				'deadline'   => array( 'type' => 'text', 'label' => 'آخر موعد للتقديم', 'desc' => 'نص حر، مثال: ٣٠ أكتوبر ٢٠٢٦' ),
				'tasks'      => array( 'type' => 'textarea', 'label' => 'المهام والمسؤوليات', 'desc' => 'كل مهمة بسطر.' ),
				'reqs'       => array( 'type' => 'textarea', 'label' => 'المؤهلات المطلوبة', 'desc' => 'كل مؤهل بسطر.' ),
				'closed'     => array( 'type' => 'switch', 'label' => 'إغلاق التقديم', 'desc' => 'تبقى الوظيفة ظاهرة مع رسالة «انتهى التقديم».' ),
			) + $banner,
		),
		'page'        => array(
			'title'  => 'بنر الصفحة',
			'fields' => $banner,
		),
		'post'        => array(
			'title'  => 'بنر المقال',
			'fields' => $banner,
		),
	);
}

/**
 * Read a meta field.
 *
 * @param string   $key     Key without prefix.
 * @param int|null $post_id Post.
 * @return string
 */
function qaf_meta( $key, $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$value   = get_post_meta( (int) $post_id, '_qaf_' . $key, true );
	return is_scalar( $value ) ? (string) $value : '';
}

/**
 * Read a structured meta field (repeater rows).
 *
 * @param string   $key     Key.
 * @param int|null $post_id Post.
 * @return array
 */
function qaf_meta_rows( $key, $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$value   = get_post_meta( (int) $post_id, '_qaf_' . $key, true );
	return is_array( $value ) ? $value : array();
}

/**
 * Attachment IDs from a gallery field.
 *
 * @param string   $key     Key.
 * @param int|null $post_id Post.
 * @return int[]
 */
function qaf_meta_gallery( $key = 'gallery', $post_id = null ) {
	return array_values( array_filter( array_map( 'absint', explode( ',', qaf_meta( $key, $post_id ) ) ) ) );
}

/**
 * Lines of a textarea field.
 *
 * @param string   $key     Key.
 * @param int|null $post_id Post.
 * @return string[]
 */
function qaf_meta_lines( $key, $post_id = null ) {
	return array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', qaf_meta( $key, $post_id ) ) ) ) );
}

/**
 * Register meta boxes.
 */
function qaf_add_meta_boxes() {
	foreach ( qaf_meta_schema() as $type => $box ) {
		add_meta_box( 'qaf-meta', $box['title'], 'qaf_render_meta_box', $type, 'normal', 'high' );
	}
}
add_action( 'add_meta_boxes', 'qaf_add_meta_boxes' );

/**
 * Render a box.
 *
 * @param WP_Post $post Post.
 */
function qaf_render_meta_box( $post ) {
	$box = qaf_meta_schema()[ $post->post_type ] ?? null;
	if ( ! $box ) {
		return;
	}
	wp_nonce_field( 'qaf_meta_save', 'qaf_meta_nonce' );
	echo '<div class="qaf-meta">';
	foreach ( $box['fields'] as $key => $field ) {
		if ( 'heading' === $field['type'] ) {
			printf( '<h3 class="qaf-meta__heading">%s</h3>', esc_html( $field['label'] ) );
			continue;
		}
		$name  = 'qaf_meta[' . $key . ']';
		$value = qaf_meta( $key, $post->ID );
		echo '<div class="qaf-meta__row">';
		printf( '<label class="qaf-meta__label" for="qaf-m-%1$s">%2$s</label><div class="qaf-meta__ctrl">', esc_attr( $key ), esc_html( $field['label'] ) );
		qaf_admin_control( $field, $name, $value, 'qaf-m-' . $key );
		if ( ! empty( $field['desc'] ) ) {
			printf( '<p class="description">%s</p>', esc_html( $field['desc'] ) );
		}
		echo '</div></div>';
	}
	echo '</div>';
}

/**
 * Save.
 *
 * @param int $post_id Post.
 */
function qaf_save_meta( $post_id ) {
	if ( ! isset( $_POST['qaf_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['qaf_meta_nonce'] ) ), 'qaf_meta_save' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$box = qaf_meta_schema()[ get_post_type( $post_id ) ] ?? null;
	if ( ! $box ) {
		return;
	}
	$input = isset( $_POST['qaf_meta'] ) && is_array( $_POST['qaf_meta'] ) ? wp_unslash( $_POST['qaf_meta'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput

	foreach ( $box['fields'] as $key => $field ) {
		if ( 'heading' === $field['type'] ) {
			continue;
		}
		$value = qaf_sanitize_field( $input[ $key ] ?? ( 'switch' === $field['type'] ? 0 : '' ), $field + array( 'default' => '' ) );
		if ( '' === $value || 0 === $value || array() === $value ) {
			delete_post_meta( $post_id, '_qaf_' . $key );
		} else {
			update_post_meta( $post_id, '_qaf_' . $key, $value );
		}
	}
}
add_action( 'save_post', 'qaf_save_meta' );

/**
 * Shared admin control renderer (options page + meta boxes).
 *
 * @param array  $field Field.
 * @param string $name  Input name.
 * @param mixed  $value Value.
 * @param string $id    Id.
 * @param array  $attrs Extra attributes (dir, lang).
 */
function qaf_admin_control( $field, $name, $value, $id = '', $attrs = array() ) {
	$extra = '';
	foreach ( $attrs as $k => $v ) {
		$extra .= sprintf( ' %s="%s"', esc_attr( $k ), esc_attr( $v ) );
	}
	$id_attr = $id ? ' id="' . esc_attr( $id ) . '"' : '';

	switch ( $field['type'] ) {
		case 'textarea':
			printf( '<textarea class="large-text" rows="3" name="%s"%s%s>%s</textarea>', esc_attr( $name ), $id_attr, $extra, esc_textarea( (string) $value ) ); // phpcs:ignore WordPress.Security.EscapeOutput
			break;
		case 'switch':
			printf( '<label class="qaf-switch"><input type="hidden" name="%1$s" value="0"><input type="checkbox" name="%1$s" value="1"%2$s%3$s><span></span></label>', esc_attr( $name ), checked( ! empty( $value ), true, false ), $id_attr ); // phpcs:ignore WordPress.Security.EscapeOutput
			break;
		case 'select':
			printf( '<select name="%s"%s>', esc_attr( $name ), $id_attr ); // phpcs:ignore WordPress.Security.EscapeOutput
			foreach ( $field['options'] as $k => $label ) {
				printf( '<option value="%s"%s>%s</option>', esc_attr( $k ), selected( (string) $value, (string) $k, false ), esc_html( $label ) );
			}
			echo '</select>';
			break;
		case 'color':
			printf( '<input type="text" class="qaf-color" name="%s" value="%s" data-default-color="%s"%s>', esc_attr( $name ), esc_attr( (string) $value ), esc_attr( $field['default'] ?? '' ), $id_attr ); // phpcs:ignore WordPress.Security.EscapeOutput
			break;
		case 'range':
			printf(
				'<div class="qaf-range"><input type="range" name="%1$s" value="%2$s" min="%3$s" max="%4$s" step="%5$s"%6$s><output>%2$s</output><span>%7$s</span></div>',
				esc_attr( $name ),
				esc_attr( (string) $value ),
				esc_attr( (string) ( $field['min'] ?? 0 ) ),
				esc_attr( (string) ( $field['max'] ?? 100 ) ),
				esc_attr( (string) ( $field['step'] ?? 1 ) ),
				$id_attr, // phpcs:ignore WordPress.Security.EscapeOutput
				esc_html( $field['unit'] ?? '' )
			);
			break;
		case 'number':
			printf( '<input type="number" class="small-text" name="%s" value="%s" min="%s" max="%s"%s>', esc_attr( $name ), esc_attr( (string) $value ), esc_attr( (string) ( $field['min'] ?? '' ) ), esc_attr( (string) ( $field['max'] ?? '' ) ), $id_attr ); // phpcs:ignore WordPress.Security.EscapeOutput
			break;
		case 'image':
			$src = is_numeric( $value ) && (int) $value > 0 ? (string) wp_get_attachment_image_url( (int) $value, 'thumbnail' ) : qaf_resolve_url( (string) $value );
			printf(
				'<div class="qaf-media%5$s"><div class="qaf-media__preview">%1$s</div><input type="text" class="regular-text qaf-media__url" name="%2$s" value="%3$s" dir="ltr"%4$s><button type="button" class="button qaf-media__pick">اختيار</button><button type="button" class="button-link qaf-media__remove">إزالة</button></div>',
				$src ? '<img src="' . esc_url( $src ) . '" alt="">' : '',
				esc_attr( $name ),
				esc_attr( (string) $value ),
				$id_attr, // phpcs:ignore WordPress.Security.EscapeOutput
				$src ? ' has-image' : ''
			);
			break;
		case 'gallery':
			$ids = array_filter( array_map( 'absint', explode( ',', (string) $value ) ) );
			echo '<div class="qaf-gallery"><ul class="qaf-gallery__list">';
			foreach ( $ids as $att ) {
				printf( '<li data-id="%1$d">%2$s<button type="button" class="qaf-gallery__x" aria-label="remove">×</button></li>', (int) $att, wp_get_attachment_image( $att, 'thumbnail' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
			}
			printf( '</ul><input type="hidden" class="qaf-gallery__ids" name="%s" value="%s"><button type="button" class="button qaf-gallery__add">إضافة صور</button></div>', esc_attr( $name ), esc_attr( implode( ',', $ids ) ) );
			break;
		case 'repeater':
			$rows = is_array( $value ) ? array_values( $value ) : array();
			echo '<div class="qaf-repeater" data-name="' . esc_attr( $name ) . '"><ol class="qaf-repeater__rows">';
			foreach ( $rows as $i => $row ) {
				qaf_admin_repeater_row( $field, $name, (int) $i, (array) $row );
			}
			echo '</ol><script type="text/html" class="qaf-repeater__tpl">';
			qaf_admin_repeater_row( $field, $name, '__i__', array() );
			echo '</script><button type="button" class="button qaf-repeater__add">+ إضافة صف</button></div>';
			break;
		case 'url':
		case 'email':
			printf( '<input type="%s" class="regular-text" name="%s" value="%s" dir="ltr"%s>', 'email' === $field['type'] ? 'email' : 'text', esc_attr( $name ), esc_attr( (string) $value ), $id_attr ); // phpcs:ignore WordPress.Security.EscapeOutput
			break;
		default:
			printf( '<input type="text" class="regular-text" name="%s" value="%s"%s%s>', esc_attr( $name ), esc_attr( (string) $value ), $id_attr, $extra ); // phpcs:ignore WordPress.Security.EscapeOutput
	}
}

/**
 * One repeater row.
 *
 * @param array      $field Repeater field.
 * @param string     $name  Base name.
 * @param int|string $i     Row index or template token.
 * @param array      $row   Values.
 */
function qaf_admin_repeater_row( $field, $name, $i, $row ) {
	echo '<li class="qaf-repeater__row"><span class="qaf-repeater__handle dashicons dashicons-menu" aria-hidden="true"></span><div class="qaf-repeater__fields">';
	foreach ( $field['fields'] as $sub_key => $sub ) {
		echo '<label class="qaf-repeater__field"><span>' . esc_html( $sub['label'] ) . '</span>';
		qaf_admin_control( $sub, $name . '[' . $i . '][' . $sub_key . ']', $row[ $sub_key ] ?? '' );
		echo '</label>';
	}
	echo '</div><button type="button" class="qaf-repeater__remove" aria-label="remove"><span class="dashicons dashicons-trash"></span></button></li>';
}

/**
 * One-time migration from v1 meta keys (q_*) to v2 (_qaf_*), so nothing entered on localhost is lost.
 */
function qaf_migrate_v1_meta() {
	if ( get_option( 'qaf_v1_migrated' ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	global $wpdb;
	$map = array(
		'q_company_en'    => '_qaf_en_name',
		'q_company_color' => '_qaf_color',
		'q_company_url'   => '_qaf_shop_url',
		'q_company_cats'  => '_qaf_specialty',
		'q_branch_city'   => '_qaf_city',
		'q_branch_addr'   => '_qaf_address',
		'q_branch_phone'  => '_qaf_phone',
		'q_branch_hours'  => '_qaf_hours',
		'q_branch_map'    => '_qaf_map_url',
		'q_job_deadline'  => '_qaf_deadline',
	);
	foreach ( $map as $old => $new ) {
		$rows = $wpdb->get_results( $wpdb->prepare( "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s", $old ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		foreach ( $rows as $row ) {
			if ( '' === get_post_meta( (int) $row->post_id, $new, true ) ) {
				update_post_meta( (int) $row->post_id, $new, maybe_unserialize( $row->meta_value ) );
			}
		}
	}
	update_option( 'qaf_v1_migrated', 1, false );
}
add_action( 'admin_init', 'qaf_migrate_v1_meta' );

/**
 * v2.1 → v2.2: job department / location / type moved from text meta to taxonomies.
 */
function qaf_migrate_job_terms() {
	if ( get_option( 'qaf_v22_jobs' ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$map = array( '_qaf_dept' => 'qaf_job_dept', '_qaf_location' => 'qaf_job_loc', '_qaf_type' => 'qaf_job_type' );
	$ids = get_posts( array( 'post_type' => 'qaf_job', 'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids', 'suppress_filters' => true ) );
	foreach ( $ids as $id ) {
		foreach ( $map as $meta => $tax ) {
			$value = trim( (string) get_post_meta( $id, $meta, true ) );
			if ( '' !== $value && ! wp_get_object_terms( $id, $tax, array( 'fields' => 'ids' ) ) ) {
				wp_set_object_terms( $id, $value, $tax );
			}
		}
	}
	update_option( 'qaf_v22_jobs', 1, false );
}
add_action( 'admin_init', 'qaf_migrate_job_terms', 20 );
