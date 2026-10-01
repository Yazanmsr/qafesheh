<?php
/**
 * Content importer — one click builds the whole site in BOTH languages.
 *
 * Rules (safe to run again at any time):
 * - Merge by stable import key, then by title — never by position.
 * - Text is refreshed only if nobody edited it since the last import (content hash).
 * - Manual work is kept: replaced images/logos, edited meta, Elementor pages edited in the builder,
 *   menus that already have items, options already saved.
 * - With WPML, every item is created in each active language and linked as a translation.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

/**
 * Submenu.
 */
function qaf_import_menu() {
	add_submenu_page( 'qaf-options', 'استيراد المحتوى', 'استيراد المحتوى', 'manage_options', 'qaf-import', 'qaf_render_import_page' );
}
add_action( 'admin_menu', 'qaf_import_menu', 20 );

/**
 * Steps: key => [ label, description, callback ].
 *
 * @return array
 */
function qaf_import_steps() {
	return array(
		'media'     => array( 'الصور', 'رفع صور المصنع والمنتجات المرفقة مع الثيم إلى مكتبة الوسائط.', 'qaf_import_media' ),
		'terms'     => array( 'العلامات والتصنيفات', 'قفيشة · الجازي · مكارم + تصنيفات المقالات.', 'qaf_import_terms' ),
		'companies' => array( 'الشركات', 'البطاقات الثلاث بصورها وألوانها وشعاراتها ومواصفاتها.', 'qaf_import_companies' ),
		'services'  => array( 'الخدمات', 'الخدمات الست بأيقوناتها.', 'qaf_import_services' ),
		'branches'  => array( 'الفروع', 'قائمة الفروع الموحّدة مربوطة بالعلامات.', 'qaf_import_branches' ),
		'jobs'      => array( 'الوظائف', 'وظائف نموذجية.', 'qaf_import_jobs' ),
		'articles'  => array( 'المقالات', 'مقالات نموذجية بصورها.', 'qaf_import_articles' ),
		'pages'     => array( 'الصفحات', 'الرئيسية، من نحن، الأخبار، تواصل، الشكاوى، السياسات.', 'qaf_import_pages' ),
		'home'      => array( 'الصفحة الرئيسية', 'بناء الرئيسية بويدجتس QB في Elementor لكل لغة.', 'qaf_import_home' ),
		'inner'     => array( 'من نحن · تواصل · شكاوى', 'بناء الصفحات الثلاث بويدجتس QB في Elementor لكل لغة.', 'qaf_import_inner' ),
		'menus'     => array( 'القوائم', 'القائمة الرئيسية، الشريط العلوي، أعمدة الفوتر.', 'qaf_import_menus' ),
		'finalize'  => array( 'الإعدادات النهائية', 'الصفحة الرئيسية وصفحة المقالات، الروابط الدائمة، مسح الكاش.', 'qaf_import_finalize' ),
	);
}

/**
 * Languages to import, default first.
 *
 * @return string[]
 */
function qaf_import_langs() {
	$def = qaf_default_lang();
	$def = in_array( $def, array( 'ar', 'en' ), true ) ? $def : 'ar';

	if ( ! qaf_wpml_on() ) {
		return array( $def );
	}
	$langs = array( $def );
	foreach ( array_keys( qaf_active_languages() ) as $code ) {
		if ( in_array( $code, array( 'ar', 'en' ), true ) && ! in_array( $code, $langs, true ) ) {
			$langs[] = $code;
		}
	}
	return $langs;
}

/**
 * Is WPML running?
 *
 * @return bool
 */
function qaf_wpml_on() {
	return defined( 'ICL_SITEPRESS_VERSION' ) && has_filter( 'wpml_element_trid' );
}

/**
 * Link an element to its default-language original.
 *
 * @param int    $id        Element id (post id or term_taxonomy_id).
 * @param string $type      post_{type} | tax_{taxonomy}.
 * @param string $lang      Language.
 * @param int    $source_id Original element id (0 for the original itself).
 */
function qaf_wpml_link( $id, $type, $lang, $source_id = 0 ) {
	if ( ! qaf_wpml_on() || ! $id ) {
		return;
	}
	$trid = $source_id ? apply_filters( 'wpml_element_trid', null, $source_id, $type ) : null;
	do_action(
		'wpml_set_element_language_details',
		array(
			'element_id'           => $id,
			'element_type'         => $type,
			'trid'                 => $trid ? $trid : false,
			'language_code'        => $lang,
			'source_language_code' => $source_id ? qaf_import_langs()[0] : null,
		)
	);
}

/**
 * Language of a post (WPML) or default.
 *
 * @param int $post_id Post.
 * @return string
 */
function qaf_post_lang( $post_id ) {
	if ( ! qaf_wpml_on() ) {
		return qaf_import_langs()[0];
	}
	$details = apply_filters( 'wpml_post_language_details', null, $post_id );
	return is_array( $details ) && ! empty( $details['language_code'] ) ? $details['language_code'] : qaf_import_langs()[0];
}

/* ================================================= Page */

/**
 * Render importer page.
 */
function qaf_render_import_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$log   = get_option( 'qaf_import_log', array() );
	$langs = qaf_import_langs();
	?>
	<div class="wrap qaf-opts qaf-import" dir="rtl">
		<div class="qaf-opts__head">
			<span class="qaf-opts__mark">QG</span>
			<div class="qaf-opts__intro">
				<h2>استيراد المحتوى</h2>
				<p>زرّ واحد يبني الموقع كاملاً — آمن للتكرار، ويحافظ على أي تعديل يدوي.</p>
			</div>
			<span class="qaf-opts__pill"><?php echo esc_html( strtoupper( implode( ' · ', $langs ) ) ); ?></span>
		</div>

		<?php if ( ! qaf_wpml_on() ) : ?>
			<div class="notice notice-info inline"><p>WPML غير مفعّل — سيتم الاستيراد باللغة <strong><?php echo esc_html( strtoupper( $langs[0] ) ); ?></strong> فقط. فعّل WPML (العربية أساسية + الإنجليزية) ثم أعد الاستيراد لإنشاء النسخ الإنجليزية مربوطة تلقائياً.</p></div>
		<?php endif; ?>
		<?php if ( ! did_action( 'elementor/loaded' ) ) : ?>
			<div class="notice notice-info inline"><p>Elementor غير مفعّل — الرئيسية ستُعرض من قالب الثيم بنفس التصميم، وتقدر تبنيها بـElementor لاحقاً بإعادة خطوة «الصفحة الرئيسية».</p></div>
		<?php endif; ?>

		<div class="qaf-import__grid">
			<div class="qaf-import__card qaf-import__main">
				<h3>تشغيل كامل</h3>
				<ul class="qaf-import__rules">
					<li>الدمج بالعنوان والمفتاح — لا يُنشئ نسخاً مكررة أبداً.</li>
					<li>النص يُحدَّث فقط إذا لم يُعدَّل يدوياً منذ آخر استيراد.</li>
					<li>الصور والشعارات المستبدلة، صفحات Elementor المعدّلة، والقوائم الموجودة — تبقى كما هي.</li>
				</ul>
				<label class="qaf-import__force"><input type="checkbox" id="qaf-import-force"> إعادة بناء صفحات Elementor (الرئيسية، من نحن، تواصل، شكاوى) حتى لو عُدّلت — تُحفظ نسخة احتياطية من تعديلاتك</label>
				<button type="button" class="button button-primary button-hero" id="qaf-import-all">استيراد كل شيء</button>
				<div class="qaf-import__bar" aria-hidden="true"><span></span></div>
			</div>

			<div class="qaf-import__card">
				<h3>الخطوات</h3>
				<ol class="qaf-import__steps">
					<?php foreach ( qaf_import_steps() as $key => $step ) : ?>
						<li class="qaf-import__step" data-step="<?php echo esc_attr( $key ); ?>">
							<span class="qaf-import__state" aria-hidden="true"></span>
							<span class="qaf-import__text">
								<strong><?php echo esc_html( $step[0] ); ?></strong>
								<small><?php echo esc_html( $step[1] ); ?></small>
								<em class="qaf-import__result"><?php echo esc_html( $log[ $key ]['summary'] ?? '' ); ?></em>
							</span>
							<button type="button" class="button qaf-import__run" data-step="<?php echo esc_attr( $key ); ?>">تشغيل</button>
						</li>
					<?php endforeach; ?>
				</ol>
			</div>
		</div>
	</div>
	<?php
}

/**
 * AJAX: run one step.
 */
function qaf_ajax_import_step() {
	check_ajax_referer( 'qaf_import', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => 'forbidden' ), 403 );
	}
	$step  = isset( $_POST['step'] ) ? sanitize_key( wp_unslash( $_POST['step'] ) ) : '';
	$steps = qaf_import_steps();
	if ( ! isset( $steps[ $step ] ) || ! is_callable( $steps[ $step ][2] ) ) {
		wp_send_json_error( array( 'message' => 'unknown step' ), 400 );
	}

	if ( function_exists( 'set_time_limit' ) ) {
		@set_time_limit( 300 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
	}
	wp_raise_memory_limit( 'admin' );
	wp_defer_term_counting( true );

	$stats                       = array( 'created' => 0, 'updated' => 0, 'kept' => 0 );
	$GLOBALS['qaf_import_force'] = ! empty( $_POST['force'] ) && in_array( $step, array( 'home', 'inner' ), true );
	try {
		$extra = call_user_func_array( $steps[ $step ][2], array( &$stats ) );
	} catch ( Throwable $e ) {
		wp_defer_term_counting( false );
		wp_send_json_error( array( 'message' => $e->getMessage() ), 500 );
	}
	wp_defer_term_counting( false );

	$summary = sprintf( 'جديد %d · محدّث %d · محفوظ كما هو %d', $stats['created'], $stats['updated'], $stats['kept'] );
	if ( is_string( $extra ) && $extra ) {
		$summary .= ' — ' . $extra;
	}
	$log          = get_option( 'qaf_import_log', array() );
	$log[ $step ] = array( 'summary' => $summary, 'time' => time() );
	update_option( 'qaf_import_log', $log, false );

	wp_send_json_success( array( 'summary' => $summary ) );
}
add_action( 'wp_ajax_qaf_import_step', 'qaf_ajax_import_step' );

/* ================================================= Helpers */

/**
 * Find an imported post by key + language, then by title.
 *
 * @param string $type  Post type.
 * @param string $key   Import key.
 * @param string $lang  Language.
 * @param string $title Title.
 * @return int
 */
function qaf_import_find( $type, $key, $lang, $title ) {
	$ids = get_posts(
		array(
			'post_type'        => $type,
			'post_status'      => array( 'publish', 'draft', 'pending', 'private', 'future' ),
			'meta_key'         => '_qaf_import_key', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value'       => $key, // phpcs:ignore WordPress.DB.SlowDBQuery
			'posts_per_page'   => -1,
			'fields'           => 'ids',
			'suppress_filters' => true,
		)
	);
	foreach ( $ids as $id ) {
		if ( qaf_post_lang( $id ) === $lang ) {
			return (int) $id;
		}
	}

	$query = new WP_Query(
		array(
			'post_type'        => $type,
			'title'            => $title,
			'post_status'      => array( 'publish', 'draft', 'pending', 'private' ),
			'posts_per_page'   => 5,
			'fields'           => 'ids',
			'no_found_rows'    => true,
			'suppress_filters' => true,
		)
	);
	foreach ( $query->posts as $id ) {
		if ( qaf_post_lang( $id ) === $lang ) {
			return (int) $id;
		}
	}
	return 0;
}

/**
 * Content fingerprint.
 *
 * @param string $title   Title.
 * @param string $content Content.
 * @param string $excerpt Excerpt.
 * @return string
 */
function qaf_import_hash( $title, $content, $excerpt ) {
	return md5( trim( (string) $title ) . '|' . trim( (string) $content ) . '|' . trim( (string) $excerpt ) );
}

/**
 * Create or merge one seed item in every language.
 *
 * @param string $type  Post type.
 * @param array  $item  Seed item.
 * @param array  $stats Stats (by ref).
 * @param array  $extra Callback( $post_id, $lang, $item ) for terms etc.
 * @return array lang => post id
 */
function qaf_import_item( $type, $item, &$stats, $extra = null ) {
	$ids      = array();
	$original = 0;
	$shared   = $item['shared'] ?? array();

	foreach ( qaf_import_langs() as $lang ) {
		$l = $item[ $lang ] ?? null;
		if ( ! $l ) {
			continue;
		}
		$title   = $l['title'];
		$content = $l['content'] ?? '';
		$excerpt = $l['excerpt'] ?? '';

		do_action( 'wpml_switch_language', $lang );
		$id = qaf_import_find( $type, $item['key'], $lang, $title );

		$postarr = array(
			'post_type'   => $type,
			'post_status' => 'publish',
			'menu_order'  => (int) ( $shared['order'] ?? 0 ),
		);
		if ( ! empty( $l['slug'] ) ) {
			$postarr['post_name'] = $l['slug'];
		}
		/* Clean Latin URLs for every language (Arabic titles would produce long encoded slugs). */
		$latin_slug = qaf_import_slug( $item );
		if ( ! empty( $shared['date'] ) ) {
			$postarr['post_date'] = $shared['date'];
		}

		if ( $id ) {
			$post       = get_post( $id );
			$last_hash  = get_post_meta( $id, '_qaf_import_hash', true );
			$current    = qaf_import_hash( $post->post_title, $post->post_content, $post->post_excerpt );
			$untouched  = ! $last_hash || $last_hash === $current;
			$seed_hash  = qaf_import_hash( $title, $content, $excerpt );

			if ( $untouched && $current !== $seed_hash ) {
				wp_update_post( $postarr + array( 'ID' => $id, 'post_title' => $title, 'post_content' => $content, 'post_excerpt' => $excerpt ) );
				update_post_meta( $id, '_qaf_import_hash', $seed_hash );
				++$stats['updated'];
			} else {
				if ( ! $last_hash ) {
					update_post_meta( $id, '_qaf_import_hash', $current );
				}
				++$stats['kept'];
			}
		} else {
			if ( empty( $postarr['post_name'] ) && $latin_slug ) {
				$postarr['post_name'] = $latin_slug;
			}
			$id = wp_insert_post( $postarr + array( 'post_title' => $title, 'post_content' => $content, 'post_excerpt' => $excerpt ), true );
			if ( is_wp_error( $id ) ) {
				continue;
			}
			update_post_meta( $id, '_qaf_import_hash', qaf_import_hash( $title, $content, $excerpt ) );
			++$stats['created'];
		}

		update_post_meta( $id, '_qaf_import_key', $item['key'] );
		qaf_import_meta( $id, array_merge( $shared['meta'] ?? array(), $l['meta'] ?? array() ) );

		if ( ! empty( $shared['image'] ) ) {
			qaf_import_thumbnail( $id, $shared['image'] );
		}

		qaf_wpml_link( $id, 'post_' . $type, $lang, $original );
		if ( ! $original ) {
			$original = $id;
		}
		if ( is_callable( $extra ) ) {
			call_user_func( $extra, $id, $lang, $item );
		}
		$ids[ $lang ] = $id;
	}
	do_action( 'wpml_switch_language', null );

	return $ids;
}

/**
 * Latin slug for a seed item: explicit slug → English title → key.
 *
 * @param array $item Seed item.
 * @return string
 */
function qaf_import_slug( $item ) {
	$map = array(
		'company-qafesheh' => 'qafesheh',
		'company-aljazi'   => 'aljazi',
		'company-makarem'  => 'makarem',
	);
	if ( isset( $map[ $item['key'] ] ) ) {
		return $map[ $item['key'] ];
	}
	if ( ! empty( $item['en']['title'] ) ) {
		return sanitize_title( remove_accents( $item['en']['title'] ) );
	}
	return sanitize_title( preg_replace( '/^[a-z]+-/', '', $item['key'] ) );
}

/**
 * Meta: write only when empty or still equal to what the importer wrote last time.
 *
 * @param int   $id   Post.
 * @param array $meta key => value.
 */
function qaf_import_meta( $id, $meta ) {
	$last = get_post_meta( $id, '_qaf_import_meta', true );
	$last = is_array( $last ) ? $last : array();

	foreach ( $meta as $key => $value ) {
		$value   = qaf_import_meta_value( $key, $value );
		$current = get_post_meta( $id, '_qaf_' . $key, true );
		if ( '' === $current || array() === $current || ( isset( $last[ $key ] ) && maybe_serialize( $last[ $key ] ) === maybe_serialize( $current ) ) ) {
			update_post_meta( $id, '_qaf_' . $key, $value );
			$last[ $key ] = $value;
		}
	}
	update_post_meta( $id, '_qaf_import_meta', $last );
}

/**
 * Convert bundled file names inside structured meta to attachment IDs.
 *
 * @param string $key   Meta key.
 * @param mixed  $value Seed value.
 * @return mixed
 */
function qaf_import_meta_value( $key, $value ) {
	if ( 'gallery' === $key && is_array( $value ) ) {
		return implode( ',', array_filter( array_map( 'qaf_imported_media_id', $value ) ) );
	}
	if ( 'categories' === $key && is_array( $value ) ) {
		foreach ( $value as &$row ) {
			if ( ! empty( $row['image'] ) && ! is_numeric( $row['image'] ) ) {
				$row['image'] = qaf_imported_media_id( $row['image'] );
			}
		}
		unset( $row );
	}
	return $value;
}

/**
 * Featured image: set when missing or still the one we imported.
 *
 * @param int    $id   Post.
 * @param string $file Bundled file.
 */
function qaf_import_thumbnail( $id, $file ) {
	$attachment = qaf_imported_media_id( $file );
	if ( ! $attachment ) {
		return;
	}
	$current = (int) get_post_thumbnail_id( $id );
	$last    = (int) get_post_meta( $id, '_qaf_import_thumb', true );
	if ( ! $current || $current === $last ) {
		set_post_thumbnail( $id, $attachment );
		update_post_meta( $id, '_qaf_import_thumb', $attachment );
	}
}

/**
 * Term in a language, linked as translation.
 *
 * @param string $taxonomy Taxonomy.
 * @param string $slug     Base slug.
 * @param array  $names    lang => name.
 * @return array lang => term_id
 */
function qaf_import_term( $taxonomy, $slug, $names ) {
	$ids      = array();
	$original = 0;
	$langs    = qaf_import_langs();

	foreach ( $langs as $i => $lang ) {
		if ( empty( $names[ $lang ] ) ) {
			continue;
		}
		$term_slug = 0 === $i ? $slug : $slug . '-' . $lang;
		do_action( 'wpml_switch_language', $lang );
		$term = get_term_by( 'slug', $term_slug, $taxonomy );
		if ( ! $term ) {
			$res = wp_insert_term( $names[ $lang ], $taxonomy, array( 'slug' => $term_slug ) );
			if ( is_wp_error( $res ) ) {
				$existing = $res->get_error_data( 'term_exists' );
				if ( ! $existing ) {
					continue;
				}
				$term = get_term( (int) $existing, $taxonomy );
			} else {
				$term = get_term( (int) $res['term_id'], $taxonomy );
			}
		}
		if ( ! $term || is_wp_error( $term ) ) {
			continue;
		}
		qaf_wpml_link( (int) $term->term_taxonomy_id, 'tax_' . $taxonomy, $lang, $original );
		if ( ! $original ) {
			$original = (int) $term->term_taxonomy_id;
		}
		$ids[ $lang ] = (int) $term->term_id;
	}
	do_action( 'wpml_switch_language', null );
	return $ids;
}

/* ================================================= Steps */

/**
 * Media.
 *
 * @param array $stats Stats.
 */
function qaf_import_media( &$stats ) {
	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';

	$map   = get_option( 'qaf_import_media', array() );
	$map   = is_array( $map ) ? $map : array();
	$files = glob( QAF_DIR . 'assets/import/*.{jpg,jpeg,png,webp}', GLOB_BRACE );

	foreach ( (array) $files as $path ) {
		$name = basename( $path );
		if ( ! empty( $map[ $name ] ) && wp_attachment_is_image( (int) $map[ $name ] ) ) {
			++$stats['kept'];
			continue;
		}
		$bits = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		if ( false === $bits ) {
			continue;
		}
		$upload = wp_upload_bits( $name, null, $bits );
		if ( ! empty( $upload['error'] ) ) {
			throw new RuntimeException( 'Upload failed: ' . $upload['error'] );
		}
		$type = wp_check_filetype( $upload['file'] );
		$alt  = ucwords( str_replace( array( '-', '_' ), ' ', pathinfo( $name, PATHINFO_FILENAME ) ) );
		$id   = wp_insert_attachment(
			array(
				'post_mime_type' => $type['type'],
				'post_title'     => $alt,
				'post_status'    => 'inherit',
			),
			$upload['file']
		);
		if ( is_wp_error( $id ) || ! $id ) {
			continue;
		}
		wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );
		update_post_meta( $id, '_wp_attachment_image_alt', $alt );
		$map[ $name ] = (int) $id;
		update_option( 'qaf_import_media', $map, false );
		++$stats['created'];
	}
	return '';
}

/**
 * Brands + article categories.
 *
 * @param array $stats Stats.
 */
function qaf_import_terms( &$stats ) {
	foreach ( qaf_seed_brands() as $slug => $names ) {
		qaf_import_term( 'qaf_brand', $slug, $names );
		++$stats['updated'];
	}
	foreach ( qaf_seed_articles() as $article ) {
		qaf_import_term( 'category', $article['shared']['cat_slug'], array( 'ar' => $article['ar']['cat'], 'en' => $article['en']['cat'] ) );
	}
	return '';
}

/**
 * Brand term ids for a language.
 *
 * @param string[] $slugs Base slugs.
 * @param string   $lang  Language.
 * @return int[]
 */
function qaf_import_brand_ids( $slugs, $lang ) {
	$names = qaf_seed_brands();
	$ids   = array();
	foreach ( (array) $slugs as $slug ) {
		if ( isset( $names[ $slug ] ) ) {
			$terms = qaf_import_term( 'qaf_brand', $slug, $names[ $slug ] );
			if ( ! empty( $terms[ $lang ] ) ) {
				$ids[] = $terms[ $lang ];
			}
		}
	}
	return $ids;
}

/**
 * Companies.
 *
 * @param array $stats Stats.
 */
function qaf_import_companies( &$stats ) {
	foreach ( qaf_seed_companies() as $item ) {
		qaf_import_item(
			'qaf_company',
			$item,
			$stats,
			static function ( $id, $lang, $item ) {
				wp_set_object_terms( $id, qaf_import_brand_ids( array( $item['shared']['brand'] ), $lang ), 'qaf_brand' );
			}
		);
	}
	return '';
}

/**
 * Services.
 *
 * @param array $stats Stats.
 */
function qaf_import_services( &$stats ) {
	foreach ( qaf_seed_services() as $item ) {
		qaf_import_item( 'qaf_service', $item, $stats );
	}
	return '';
}

/**
 * Branches.
 *
 * @param array $stats Stats.
 */
function qaf_import_branches( &$stats ) {
	foreach ( qaf_seed_branches() as $item ) {
		qaf_import_item(
			'qaf_branch',
			$item,
			$stats,
			static function ( $id, $lang, $item ) {
				wp_set_object_terms( $id, qaf_import_brand_ids( $item['shared']['brands'], $lang ), 'qaf_brand' );
			}
		);
	}
	return '';
}

/**
 * Jobs.
 *
 * @param array $stats Stats.
 */
function qaf_import_jobs( &$stats ) {
	$terms = qaf_seed_job_terms();
	$ids   = array();
	foreach ( $terms as $tax => $list ) {
		foreach ( $list as $slug => $names ) {
			$ids[ $tax ][ $slug ] = qaf_import_term( $tax, $slug, array( 'ar' => $names[0], 'en' => $names[1] ) );
		}
	}
	foreach ( qaf_seed_jobs() as $item ) {
		qaf_import_item(
			'qaf_job',
			$item,
			$stats,
			static function ( $id, $lang, $item ) use ( $ids ) {
				foreach ( $item['shared']['terms'] as $tax => $slug ) {
					if ( ! empty( $ids[ $tax ][ $slug ][ $lang ] ) && ! wp_get_object_terms( $id, $tax, array( 'fields' => 'ids' ) ) ) {
						wp_set_object_terms( $id, array( (int) $ids[ $tax ][ $slug ][ $lang ] ), $tax );
					}
				}
			}
		);
	}
	return '';
}

/**
 * Articles.
 *
 * @param array $stats Stats.
 */
function qaf_import_articles( &$stats ) {
	foreach ( qaf_seed_articles() as $item ) {
		qaf_import_item(
			'post',
			$item,
			$stats,
			static function ( $id, $lang, $item ) {
				$terms = qaf_import_term( 'category', $item['shared']['cat_slug'], array( 'ar' => $item['ar']['cat'], 'en' => $item['en']['cat'] ) );
				if ( ! empty( $terms[ $lang ] ) ) {
					wp_set_post_categories( $id, array( $terms[ $lang ] ) );
				}
			}
		);
	}
	return '';
}

/**
 * Pages.
 *
 * @param array $stats Stats.
 */
function qaf_import_pages( &$stats ) {
	$map = array();
	foreach ( qaf_seed_pages() as $item ) {
		$map[ $item['key'] ] = qaf_import_item( 'page', $item, $stats );
	}
	update_option( 'qaf_import_pages', $map, false );
	return '';
}

/**
 * Page id for a seed key + language.
 *
 * @param string $key  Seed key.
 * @param string $lang Language.
 * @return int
 */
function qaf_import_page_id( $key, $lang ) {
	$map = get_option( 'qaf_import_pages', array() );
	$id  = absint( $map[ $key ][ $lang ] ?? 0 );
	return ( $id && get_post( $id ) ) ? $id : 0;
}

/**
 * Permalink of a CPT archive in a language.
 *
 * @param string $type Post type.
 * @param string $lang Language.
 * @return string
 */
function qaf_import_archive_url( $type, $lang ) {
	$url = get_post_type_archive_link( $type );
	return (string) apply_filters( 'wpml_permalink', $url, $lang, true );
}

/**
 * Elementor element id.
 *
 * @return string
 */
function qaf_el_id() {
	return substr( md5( wp_generate_uuid4() ), 0, 7 );
}

/**
 * Elementor URL control value.
 *
 * @param string $url URL.
 * @return array
 */
function qaf_el_url( $url ) {
	return array( 'url' => (string) $url, 'is_external' => '', 'nofollow' => '', 'custom_attributes' => '' );
}

/**
 * Elementor media control value.
 *
 * @param string $file Bundled file.
 * @return array
 */
function qaf_el_media( $file ) {
	$id = qaf_imported_media_id( $file );
	return array( 'url' => qaf_image_url( $file ), 'id' => $id ? $id : '', 'source' => 'library' );
}

/**
 * Repeater rows with Elementor _id.
 *
 * @param array $rows Rows.
 * @return array
 */
function qaf_el_rows( $rows ) {
	return array_map( static fn( $r ) => array( '_id' => qaf_el_id() ) + $r, (array) $rows );
}

/**
 * Build Elementor data for the home page in a language.
 *
 * @param string $lang Language.
 * @return array
 */
function qaf_build_home_elementor( $lang ) {
	$h       = qaf_seed_home( $lang );
	$about   = qaf_import_page_id( 'page-about', $lang );
	$contact = qaf_import_page_id( 'page-contact', $lang );

	$widgets = array(
		'qb-hero'         => array(
			'slides'   => qaf_el_rows(
				array_map(
					static function ( $slide ) use ( $lang ) {
						$fix = static function ( $url ) use ( $lang ) {
							if ( '' === $url || '#' === $url[0] ) {
								return qaf_el_url( $url );
							}
							$map = array( '/about/' => 'page-about', '/en/about/' => 'page-about' );
							if ( isset( $map[ $url ] ) && qaf_import_page_id( $map[ $url ], $lang ) ) {
								return qaf_el_url( get_permalink( qaf_import_page_id( $map[ $url ], $lang ) ) );
							}
							foreach ( array( 'services' => 'qaf_service', 'careers' => 'qaf_job', 'branches' => 'qaf_branch' ) as $slug => $type ) {
								if ( false !== strpos( $url, '/' . $slug . '/' ) ) {
									return qaf_el_url( qaf_import_archive_url( $type, $lang ) );
								}
							}
							return qaf_el_url( $url );
						};
						return array(
							'image'    => qaf_el_media( $slide['image'] ),
							'position' => $slide['position'],
							'eyebrow'  => $slide['eyebrow'],
							'title'    => $slide['title'],
							'text'     => $slide['text'],
							'btn1'     => $slide['btn1'],
							'btn1_url' => $fix( $slide['btn1_url'] ),
							'btn2'     => $slide['btn2'],
							'btn2_url' => $fix( $slide['btn2_url'] ),
						);
					},
					$h['hero']['slides']
				)
			),
			'stats'    => qaf_el_rows( $h['hero']['stats'] ),
			'grid'     => 'yes',
			'autoplay' => 'yes',
			'interval' => 7000,
		),
		'qb-statement'    => array(
			'num'      => $h['statement']['num'],
			'label'    => $h['statement']['label'],
			'title'    => $h['statement']['title'],
			'col1'     => $h['statement']['col1'],
			'col2'     => $h['statement']['col2'],
			'link'     => $h['statement']['link'],
			'link_url' => qaf_el_url( $about ? get_permalink( $about ) : '' ),
		),
		'qb-companies'    => array(
			'num'    => $h['companies']['num'],
			'label'  => $h['companies']['label'],
			'title'  => $h['companies']['title'],
			'text'   => $h['companies']['text'],
			'count'  => 3,
			'anchor' => 'companies',
		),
		'qb-offers'       => array(
			'num'    => '',
			'label'  => 'en' === ( $lang ?? 'ar' ) ? 'Current offers' : 'عروض الشركات',
			'title'  => 'en' === ( $lang ?? 'ar' ) ? "From our three houses,\nfor a limited time" : "من بيوتنا الثلاثة،\nلفترة محدودة",
			'limit'  => 3,
			'anchor' => 'offers',
		),
		'qb-capabilities' => array(
			'num'    => $h['capabilities']['num'],
			'label'  => $h['capabilities']['label'],
			'title'  => $h['capabilities']['title'],
			'image'  => qaf_el_media( 'factory-women.jpg' ),
			'thumb1' => qaf_el_media( 'boutique-coats.jpg' ),
			'thumb2' => qaf_el_media( 'textiles-stack.jpg' ),
			'rows'   => qaf_el_rows( $h['capabilities']['rows'] ),
		),
		'qb-services'     => array(
			'num'      => $h['services']['num'],
			'label'    => $h['services']['label'],
			'title'    => $h['services']['title'],
			'link'     => $h['services']['link'],
			'link_url' => qaf_el_url( qaf_import_archive_url( 'qaf_service', $lang ) ),
			'count'    => 6,
		),
		'qb-standards'    => array(
			'num'   => $h['standards']['num'],
			'label' => $h['standards']['label'],
			'rows'  => qaf_el_rows( $h['standards']['rows'] ),
		),
		'qb-posts'        => array(
			'num'      => $h['news']['num'],
			'label'    => $h['news']['label'],
			'title'    => $h['news']['title'],
			'link'     => $h['news']['link'],
			'read'     => 'en' === $lang ? 'Read' : 'اقرأ',
			'count'    => 3,
			'category' => 0,
		),
		'qb-branches'     => array(
			'num'   => $h['branches']['num'],
			'label' => $h['branches']['label'],
			'title' => $h['branches']['title'],
			'count' => 5,
			'brand' => '',
		),
		'qb-cta'          => array(
			'title'    => $h['cta']['title'],
			'text'     => $h['cta']['text'],
			'btn1'     => $h['cta']['btn1'],
			'btn1_url' => qaf_el_url( $contact ? get_permalink( $contact ) : '' ),
			'btn2'     => $h['cta']['btn2'],
			'btn2_url' => qaf_el_url( qaf_import_archive_url( 'qaf_job', $lang ) ),
		),
	);

	$zero = array( 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true );
	$data = array();
	foreach ( $widgets as $type => $settings ) {
		$data[] = array(
			'id'       => qaf_el_id(),
			'elType'   => 'section',
			'settings' => array( 'layout' => 'full_width', 'gap' => 'no', 'padding' => $zero, 'margin' => $zero ),
			'elements' => array(
				array(
					'id'       => qaf_el_id(),
					'elType'   => 'column',
					'settings' => array( '_column_size' => 100, '_inline_size' => null, 'padding' => $zero ),
					'elements' => array(
						array(
							'id'         => qaf_el_id(),
							'elType'     => 'widget',
							'widgetType' => $type,
							'settings'   => $settings,
							'elements'   => array(),
						),
					),
					'isInner'  => false,
				),
			),
			'isInner'  => false,
		);
	}
	return $data;
}

/**
 * Write Elementor data to a page unless someone edited it in the builder since the last import.
 *
 * @param int   $id    Page.
 * @param array $data  Elementor elements.
 * @param array $stats Stats.
 */
function qaf_import_elementor_page( $id, $data, &$stats ) {
	$current   = (string) get_post_meta( $id, '_elementor_data', true );
	$last_hash = (string) get_post_meta( $id, '_qaf_import_el_hash', true );
	$decoded   = $current ? json_decode( $current, true ) : array();
	$is_empty  = ! is_array( $decoded ) || ! $decoded;
	if ( ! qaf_import_force() && ! $is_empty && ( ! $last_hash || md5( $current ) !== $last_hash ) ) {
		++$stats['kept'];
		return;
	}
	if ( qaf_import_force() && ! $is_empty ) {
		/* Keep a restorable copy of what the user had. */
		update_post_meta( $id, '_qaf_elementor_backup', wp_slash( $current ) );
	}
	update_post_meta( $id, '_elementor_edit_mode', 'builder' );
	update_post_meta( $id, '_elementor_template_type', 'wp-page' );
	update_post_meta( $id, '_wp_page_template', 'default' );
	if ( defined( 'ELEMENTOR_VERSION' ) ) {
		update_post_meta( $id, '_elementor_version', ELEMENTOR_VERSION );
	}
	update_post_meta( $id, '_elementor_data', wp_slash( wp_json_encode( $data, JSON_UNESCAPED_UNICODE ) ) );
	update_post_meta( $id, '_qaf_import_el_hash', md5( (string) get_post_meta( $id, '_elementor_data', true ) ) );
	delete_post_meta( $id, '_elementor_css' );
	$current ? ++$stats['updated'] : ++$stats['created'];
}

/**
 * Wrap widgets into full-width Elementor sections.
 *
 * @param array $widgets [ [ type, settings ], … ].
 * @return array
 */
function qaf_el_sections( $widgets ) {
	$zero = array( 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true );
	$data = array();
	foreach ( $widgets as $w ) {
		$data[] = array(
			'id'       => qaf_el_id(),
			'elType'   => 'section',
			'settings' => array( 'layout' => 'full_width', 'gap' => 'no', 'padding' => $zero, 'margin' => $zero ),
			'elements' => array(
				array(
					'id'       => qaf_el_id(),
					'elType'   => 'column',
					'settings' => array( '_column_size' => 100, '_inline_size' => null, 'padding' => $zero ),
					'elements' => array( array( 'id' => qaf_el_id(), 'elType' => 'widget', 'widgetType' => $w[0], 'settings' => $w[1], 'elements' => array() ) ),
					'isInner'  => false,
				),
			),
			'isInner'  => false,
		);
	}
	return $data;
}

/**
 * About / Contact / Complaints in Elementor, from the same recipes page.php uses.
 *
 * @param array $stats Stats.
 */
function qaf_import_inner( &$stats ) {
	if ( ! did_action( 'elementor/loaded' ) ) {
		++$stats['kept'];
		return 'Elementor غير مفعّل — الصفحات تعمل من قوالب الثيم بنفس التصميم';
	}
	$map = array(
		'page-hero'      => 'qb-page-hero',
		'story'          => 'qb-story',
		'timeline'       => 'qb-timeline',
		'vision'         => 'qb-vision',
		'values'         => 'qb-values',
		'brands-strip'   => 'qb-brands-strip',
		'channels'       => 'qb-channels',
		'steps-bar'      => 'qb-steps-bar',
		'contact-form'   => 'qb-contact-form',
		'complaint-form' => 'qb-complaint-form',
	);
	foreach ( qaf_page_recipes() as $key => $recipe ) {
		foreach ( qaf_import_langs() as $lang ) {
			$id = qaf_import_page_id( $key, $lang );
			if ( ! $id ) {
				continue;
			}
			$copy    = qaf_seed_page_copy( $recipe[0], $lang );
			$widgets = array();
			foreach ( $recipe[1] as $section => $copy_key ) {
				$settings = $copy_key ? (array) ( $copy[ $copy_key ] ?? array() ) : array();
				if ( 'steps-bar' === $section ) {
					$settings = array( 'items' => qaf_el_rows( $settings ) );
				}
				if ( isset( $settings['items'] ) && 'steps-bar' !== $section ) {
					$settings['items'] = qaf_el_rows( $settings['items'] );
				}
				if ( isset( $settings['image'] ) ) {
					$settings['image'] = qaf_el_media( $settings['image'] );
				}
				foreach ( array( 'brands', 'promo', 'map', 'pledge', 'direct', 'track' ) as $switch ) {
					if ( in_array( $section, array( 'contact-form', 'complaint-form' ), true ) ) {
						$settings[ $switch ] = 'yes';
					}
				}
				$widgets[] = array( $map[ $section ], $settings );
			}
			qaf_import_elementor_page( $id, qaf_el_sections( $widgets ), $stats );
		}
	}
	return '';
}

/**
 * Home page in Elementor (keeps pages edited in the builder).
 *
 * @param array $stats Stats.
 */
function qaf_import_home( &$stats ) {
	if ( ! did_action( 'elementor/loaded' ) ) {
		++$stats['kept'];
		return 'Elementor غير مفعّل — الرئيسية تعمل من قالب الثيم';
	}
	foreach ( qaf_import_langs() as $lang ) {
		$id = qaf_import_page_id( 'page-home', $lang );
		if ( ! $id ) {
			throw new RuntimeException( 'Run the Pages step first.' );
		}
		qaf_import_elementor_page( $id, qaf_build_home_elementor( $lang ), $stats );
	}
	return '';
}

/**
 * Force flag from the importer screen (rebuild Elementor pages even if edited; a backup is kept).
 *
 * @return bool
 */
function qaf_import_force() {
	return ! empty( $GLOBALS['qaf_import_force'] );
}

/**
 * Menus.
 *
 * @param array $stats Stats.
 */
function qaf_import_menus( &$stats ) {
	$locations = get_theme_mod( 'nav_menu_locations', array() );
	$langs     = qaf_import_langs();
	$labels    = array(
		'primary'        => 'Primary',
		'utility'        => 'Top bar',
		'footer_group'   => 'Footer — Group',
		'footer_brands'  => 'Footer — Companies',
		'footer_service' => 'Footer — Service',
		'footer_legal'   => 'Footer — Legal',
	);

	foreach ( qaf_seed_menus() as $location => $items ) {
		$original = 0;
		foreach ( $langs as $i => $lang ) {
			$name = $labels[ $location ] . ' (' . strtoupper( $lang ) . ')';
			$menu = wp_get_nav_menu_object( $name );
			if ( ! $menu ) {
				$menu_id = wp_create_nav_menu( $name );
				if ( is_wp_error( $menu_id ) ) {
					continue;
				}
				$menu = wp_get_nav_menu_object( $menu_id );
			}

			if ( wp_get_nav_menu_items( $menu->term_id ) ) {
				++$stats['kept'];
			} else {
				foreach ( $items as $pos => $entry ) {
					qaf_import_menu_item( (int) $menu->term_id, $entry, $lang, $i, $pos + 1 );
				}
				++$stats['created'];
			}

			qaf_wpml_link( (int) $menu->term_taxonomy_id, 'tax_nav_menu', $lang, $original );
			if ( 0 === $i ) {
				$original                = (int) $menu->term_taxonomy_id;
				$locations[ $location ] = (int) $menu->term_id;
			}
		}
	}
	set_theme_mod( 'nav_menu_locations', $locations );
	return '';
}

/**
 * Add one menu item.
 *
 * @param int    $menu_id Menu.
 * @param array  $entry   [ type, ref, ar, en ].
 * @param string $lang    Language.
 * @param int    $index   Language index (0 = default).
 * @param int    $pos     Position.
 */
function qaf_import_menu_item( $menu_id, $entry, $lang, $index, $pos ) {
	list( $type, $ref, $label_ar, $label_en ) = $entry;
	$title = 'en' === $lang ? $label_en : $label_ar;
	$args  = array(
		'menu-item-title'    => $title,
		'menu-item-status'   => 'publish',
		'menu-item-position' => $pos,
	);

	if ( 'page' === $type ) {
		$id = qaf_import_page_id( $ref, $lang );
		if ( ! $id ) {
			return;
		}
		$args += array( 'menu-item-object' => 'page', 'menu-item-object-id' => $id, 'menu-item-type' => 'post_type' );
	} elseif ( 'post' === $type ) {
		$type_guess = 0 === strpos( $ref, 'company-' ) ? 'qaf_company' : 'post';
		$ids        = get_posts( array( 'post_type' => $type_guess, 'meta_key' => '_qaf_import_key', 'meta_value' => $ref, 'fields' => 'ids', 'posts_per_page' => -1, 'suppress_filters' => true ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
		$found      = 0;
		foreach ( $ids as $id ) {
			if ( qaf_post_lang( $id ) === $lang ) {
				$found = $id;
				break;
			}
		}
		if ( ! $found ) {
			return;
		}
		$args += array( 'menu-item-object' => $type_guess, 'menu-item-object-id' => $found, 'menu-item-type' => 'post_type' );
	} elseif ( 'archive' === $type ) {
		if ( 0 === $index ) {
			$args += array( 'menu-item-object' => $ref, 'menu-item-type' => 'post_type_archive' );
		} else {
			$args += array( 'menu-item-type' => 'custom', 'menu-item-url' => qaf_import_archive_url( $ref, $lang ) );
		}
	} else {
		$args += array( 'menu-item-type' => 'custom', 'menu-item-url' => $ref );
	}

	wp_update_nav_menu_item( $menu_id, 0, $args );
}

/**
 * Reading settings, permalinks, caches.
 *
 * @param array $stats Stats.
 */
function qaf_import_finalize( &$stats ) {
	$lang = qaf_import_langs()[0];
	$home = qaf_import_page_id( 'page-home', $lang );
	$news = qaf_import_page_id( 'page-news', $lang );

	if ( $home ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home );
	}
	if ( $news ) {
		update_option( 'page_for_posts', $news );
	}
	if ( ! get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
	}
	if ( 'UTC' === get_option( 'timezone_string' ) || ! get_option( 'timezone_string' ) ) {
		update_option( 'timezone_string', 'Asia/Amman' );
	}

	update_option( 'qaf_flush_rewrite', 1, false );
	flush_rewrite_rules( false );

	if ( did_action( 'elementor/loaded' ) && class_exists( '\Elementor\Plugin' ) ) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}
	do_action( 'litespeed_purge_all' );
	++$stats['updated'];
	return '';
}
