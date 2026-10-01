<?php
/**
 * Shared render helpers. Templates AND Elementor widgets call these, so both paths look identical.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

/**
 * Inline SVG icon (stroke icons, currentColor).
 *
 * @param string $name  Icon.
 * @param int    $size  Pixel size.
 * @param string $class Extra class.
 * @return string
 */
function qaf_icon( $name, $size = 24, $class = '' ) {
	$paths = array(
		'arrow'     => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'arrow-up'  => '<path d="M12 19V5M6 11l6-6 6 6"/>',
		'menu'      => '<path d="M4 7h16M4 12h16M4 17h10"/>',
		'close'     => '<path d="M6 6l12 12M18 6L6 18"/>',
		'phone'     => '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 005 5L15 13l5 2v4a2 2 0 01-2 2A16 16 0 013 6a2 2 0 012-2z"/>',
		'mail'      => '<rect x="3" y="5" width="18" height="14"/><path d="M3 7l9 6 9-6"/>',
		'pin'       => '<path d="M12 21s-7-6.2-7-11.5A7 7 0 0112 2.5a7 7 0 017 7C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/>',
		'clock'     => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
		'external'  => '<path d="M14 4h6v6M20 4l-9 9M18 14v6H4V6h6"/>',
		'factory'   => '<path d="M4 21V9l6 4V9l6 4V5l4 2v14z"/>',
		'tailor'    => '<path d="M6 3l6 4 6-4v7a6 6 0 01-12 0z"/><path d="M9 14l-2 7M15 14l2 7"/>',
		'truck'     => '<rect x="3" y="7" width="13" height="10"/><path d="M16 10h3l2 3v4h-5z"/><circle cx="7" cy="18" r="1.8"/><circle cx="18" cy="18" r="1.8"/>',
		'globe'     => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.6 2.8 2.6 15.2 0 18M12 3c-2.6 2.8-2.6 15.2 0 18"/>',
		'box'       => '<path d="M4 7h16v13H4z"/><path d="M9 7V4h6v3"/><path d="M9 13h6"/>',
		'star'      => '<path d="M12 3l2.6 5.7 6.4.7-4.8 4.4 1.3 6.2L12 17l-5.5 3 1.3-6.2L3 9.4l6.4-.7z"/>',
		'needle'    => '<path d="M20 4L8 16M8 16l-3 4 4-3M16 4h4v4"/><path d="M4 12c3-1 5 1 8 0"/>',
		'pen'       => '<path d="M4 20l4-1 11-11-3-3L5 16z"/><path d="M14 6l3 3"/>',
		'instagram' => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1"/>',
		'facebook'  => '<path d="M14 8h3V4h-3c-2.2 0-4 1.8-4 4v2H7v4h3v6h4v-6h3l1-4h-4V8z"/>',
		'youtube'   => '<rect x="2" y="5" width="20" height="14" rx="4"/><path d="M10 9l5 3-5 3z"/>',
		'tiktok'    => '<path d="M14 3v11.5a3.5 3.5 0 11-3.5-3.5"/><path d="M14 3c.6 2.8 2.6 4.6 5 5"/>',
		'whatsapp'  => '<path d="M4 20l1.3-4A8 8 0 1112 20a8 8 0 01-4-1.1z"/><path d="M9 9c0 3 3 6 6 6l1-1.5-2-1-1 .8c-1-.4-2-1.4-2.4-2.4l.8-1-1-2z"/>',
		'search'    => '<circle cx="11" cy="11" r="7"/><path d="M20 20l-4-4"/>',
		'chart'     => '<path d="M4 19V5M4 19h16M8 15V9M12 15V7M16 15v-4"/>',
		'shield'    => '<path d="M12 3l8 3v6c0 4.5-3.4 8-8 9-4.6-1-8-4.5-8-9V6z"/>',
		'heart'     => '<path d="M12 20s-7-4.4-7-10a4 4 0 017-2.6A4 4 0 0119 10c0 5.6-7 10-7 10z"/>',
		'x'         => '<path d="M4 4l16 16M20 4L4 20"/>',
		'link'      => '<path d="M10 14a4 4 0 005.7 0l3-3a4 4 0 00-5.7-5.7l-1 1"/><path d="M14 10a4 4 0 00-5.7 0l-3 3a4 4 0 005.7 5.7l1-1"/>',
		'upload'    => '<path d="M12 16V4M7 9l5-5 5 5"/><path d="M4 16v4h16v-4"/>',
		'file'      => '<path d="M6 3h8l4 4v14H6z"/><path d="M14 3v4h4"/>',
		'check'     => '<path d="M5 12l5 5 9-10"/>',
		'spark'     => '<path d="M12 2l3 7 7 3-7 3-3 7-3-7-7-3 7-3z"/>',
		'quote'     => '<path d="M7 7h4v4c0 2.5-1.6 4.3-4 5V13H7zM15 7h4v4c0 2.5-1.6 4.3-4 5V13z"/>',
	);
	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}
	return sprintf(
		'<svg class="qaf-icon qaf-icon--%1$s %2$s" width="%3$d" height="%3$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%4$s</svg>',
		esc_attr( $name ),
		esc_attr( $class ),
		absint( $size ),
		$paths[ $name ]
	);
}

/**
 * Eight-point star path (the group motif).
 *
 * @return string
 */
function qaf_star_path() {
	return 'M64 12 L79 49 L116 64 L79 79 L64 116 L49 79 L12 64 L49 49 Z';
}

/**
 * Entrance animation attributes.
 *
 * @param string $type  up|down|start|end|fade|zoom|reveal|blur|line.
 * @param int    $delay Delay (ms).
 * @return string
 */
function qaf_anim( $type = 'up', $delay = 0 ) {
	$out = ' data-qaf-anim="' . esc_attr( $type ) . '"';
	if ( $delay ) {
		$out .= ' style="--qaf-delay:' . absint( $delay ) . 'ms"';
	}
	return $out;
}

/**
 * Escape a multi-line title, turning newlines into <br>.
 *
 * @param string $text Text.
 * @return string
 */
function qaf_lines( $text ) {
	$parts = preg_split( '/\r\n|\r|\n/', (string) $text );
	return implode( '<br>', array_map( 'esc_html', $parts ) );
}

/**
 * Resolve theme-relative / site-relative link.
 *
 * @param string $url URL.
 * @return string
 */
function qaf_link( $url ) {
	$url = trim( (string) $url );
	if ( '' === $url ) {
		return '';
	}
	if ( '#' === $url[0] ) {
		return $url;
	}
	return esc_url( qaf_resolve_url( $url ) );
}

/**
 * Image tag from attachment ID, theme-relative path or absolute URL.
 *
 * @param int|string $image Image.
 * @param string     $size  Size for attachments.
 * @param array      $attrs Attributes (class, alt, loading, fetchpriority, sizes, style).
 * @return string
 */
function qaf_image( $image, $size = 'large', $attrs = array() ) {
	$attrs = wp_parse_args( $attrs, array( 'alt' => '', 'loading' => 'lazy', 'decoding' => 'async' ) );
	if ( 'eager' === $attrs['loading'] ) {
		unset( $attrs['decoding'] );
	}

	if ( is_numeric( $image ) && (int) $image > 0 ) {
		return wp_get_attachment_image( (int) $image, $size, false, $attrs );
	}

	$image = (string) $image;
	if ( '' === $image ) {
		return '';
	}

	/* A demo file that was already imported → use the attachment for srcset. */
	$attachment = qaf_imported_media_id( $image );
	if ( $attachment ) {
		return wp_get_attachment_image( $attachment, $size, false, $attrs );
	}

	$src  = qaf_resolve_url( false === strpos( $image, '/' ) ? 'assets/import/' . $image : $image );
	$path = '';
	if ( 0 === strpos( $src, QAF_URI ) ) {
		$path = QAF_DIR . substr( $src, strlen( QAF_URI ) );
	}
	if ( $path && file_exists( $path ) ) {
		$dim = wp_getimagesize( $path );
		if ( $dim ) {
			$attrs['width']  = $dim[0];
			$attrs['height'] = $dim[1];
		}
	}

	$html = '<img src="' . esc_url( $src ) . '"';
	foreach ( $attrs as $k => $v ) {
		$html .= ' ' . esc_attr( $k ) . '="' . esc_attr( (string) $v ) . '"';
	}
	return $html . '>';
}

/**
 * Plain image URL.
 *
 * @param int|string $image Image.
 * @param string     $size  Size.
 * @return string
 */
function qaf_image_url( $image, $size = 'large' ) {
	if ( is_numeric( $image ) && (int) $image > 0 ) {
		return (string) wp_get_attachment_image_url( (int) $image, $size );
	}
	$image = (string) $image;
	if ( '' === $image ) {
		return '';
	}
	$attachment = qaf_imported_media_id( $image );
	if ( $attachment ) {
		return (string) wp_get_attachment_image_url( $attachment, $size );
	}
	return qaf_resolve_url( false === strpos( $image, '/' ) ? 'assets/import/' . $image : $image );
}

/**
 * Attachment created by the importer for a bundled demo file.
 *
 * @param string $file File name.
 * @return int
 */
function qaf_imported_media_id( $file ) {
	$map = get_option( 'qaf_import_media', array() );
	$key = basename( (string) $file );
	$id  = is_array( $map ) ? absint( $map[ $key ] ?? 0 ) : 0;
	return ( $id && wp_attachment_is_image( $id ) ) ? $id : 0;
}

/**
 * Chapter rail: numbered plate + label + rule.
 *
 * @param string $num   Number.
 * @param string $label Label.
 * @param string $class Extra class.
 */
function qaf_chapter_rail( $num, $label, $class = '' ) {
	if ( '' === (string) $num && '' === (string) $label ) {
		return;
	}
	?>
	<div class="qaf-rail <?php echo esc_attr( $class ); ?>"<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
		<?php if ( '' !== (string) $num ) : ?>
			<span class="qaf-plate"><?php echo esc_html( $num ); ?></span>
		<?php endif; ?>
		<span class="qaf-rail__meta">
			<?php if ( $label ) : ?>
				<span class="qaf-rail__label"><?php echo esc_html( $label ); ?></span>
			<?php endif; ?>
			<span class="qaf-rule" aria-hidden="true"></span>
		</span>
	</div>
	<?php
}

/**
 * Underlined arrow link.
 *
 * @param string $text  Text.
 * @param string $url   URL.
 * @param string $class Extra class.
 * @param string $style Inline custom property (e.g. --qaf-accent:#fff).
 */
function qaf_arrow_link( $text, $url, $class = '', $style = '' ) {
	if ( '' === (string) $text ) {
		return;
	}
	$tag  = $url ? 'a' : 'span';
	$href = $url ? ' href="' . qaf_link( $url ) . '"' : '';
	printf(
		'<%1$s class="qaf-link %2$s"%3$s%4$s><span>%5$s</span>%6$s</%1$s>',
		$tag, // phpcs:ignore WordPress.Security.EscapeOutput
		esc_attr( $class ),
		$href, // phpcs:ignore WordPress.Security.EscapeOutput
		$style ? ' style="' . esc_attr( $style ) . '"' : '',
		esc_html( $text ),
		qaf_icon( 'arrow', 15 ) // phpcs:ignore WordPress.Security.EscapeOutput
	);
}

/**
 * Button.
 *
 * @param string $text    Text.
 * @param string $url     URL.
 * @param string $variant solid|outline|ghost.
 * @param string $class   Extra class.
 */
function qaf_button( $text, $url, $variant = 'solid', $class = '' ) {
	if ( '' === (string) $text ) {
		return;
	}
	printf(
		'<a class="qaf-btn qaf-btn--%1$s %2$s" href="%3$s"><span class="qaf-btn__label">%4$s</span></a>',
		esc_attr( $variant ),
		esc_attr( $class ),
		qaf_link( $url ? $url : '#' ), // phpcs:ignore WordPress.Security.EscapeOutput
		esc_html( $text )
	);
}

/**
 * Render a section template part with args. Single render path for templates + widgets.
 *
 * @param string $name Part name in template-parts/home.
 * @param array  $args Args.
 */
function qaf_section( $name, $args = array() ) {
	$name = sanitize_key( $name );
	$dir  = file_exists( QAF_DIR . 'template-parts/sections/' . $name . '.php' ) ? 'sections' : 'home';
	get_template_part( 'template-parts/' . $dir . '/' . $name, null, $args );
}

/**
 * Site logo + wordmark.
 *
 * @param string $context header|footer|loader.
 */
function qaf_logo( $context = 'header' ) {
	$logo  = qaf_opt_url( 'logo' );
	$name  = (string) qaf_opt_i18n( 'brand_name' );
	$sub   = (string) qaf_opt_i18n( 'brand_sub' );
	$words = qaf_opt( 'brand_text' );
	?>
	<a class="qaf-logo qaf-logo--<?php echo esc_attr( $context ); ?>" href="<?php echo esc_url( apply_filters( 'wpml_home_url', home_url( '/' ) ) ); ?>" rel="home">
		<?php if ( $logo ) : ?>
			<img class="qaf-logo__mark" src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( $name ); ?>" width="96" height="96" <?php echo 'header' === $context ? 'fetchpriority="high"' : 'loading="lazy"'; ?>>
		<?php endif; ?>
		<?php if ( $words ) : ?>
			<span class="qaf-logo__text">
				<span class="qaf-logo__name"><?php echo esc_html( $name ); ?></span>
				<?php if ( $sub ) : ?>
					<span class="qaf-logo__sub"><?php echo esc_html( $sub ); ?></span>
				<?php endif; ?>
			</span>
		<?php endif; ?>
	</a>
	<?php
}

/**
 * Nav menu with a graceful empty state.
 *
 * @param string $location Location.
 * @param string $class    Menu class.
 * @param int    $depth    Depth.
 * @return string
 */
function qaf_menu( $location, $class, $depth = 1 ) {
	if ( ! has_nav_menu( $location ) ) {
		return '';
	}
	return (string) wp_nav_menu(
		array(
			'theme_location' => $location,
			'container'      => false,
			'menu_class'     => $class,
			'depth'          => $depth,
			'fallback_cb'    => false,
			'echo'           => false,
		)
	);
}

/**
 * Language switcher: links to the other languages.
 *
 * @param string $class Class.
 */
function qaf_lang_switcher( $class = 'qaf-lang' ) {
	if ( ! qaf_opt( 'lang_switch' ) ) {
		return;
	}
	$langs = qaf_active_languages();
	if ( count( $langs ) < 2 ) {
		return;
	}
	echo '<span class="' . esc_attr( $class ) . '">';
	foreach ( $langs as $lang ) {
		if ( $lang['active'] ) {
			continue;
		}
		printf(
			'<a href="%1$s" hreflang="%2$s" lang="%2$s" data-qaf-nofx>%3$s</a>',
			esc_url( $lang['url'] ),
			esc_attr( $lang['code'] ),
			esc_html( 'ar' === $lang['code'] ? 'عربي' : strtoupper( $lang['code'] ) )
		);
	}
	echo '</span>';
}

/**
 * Social icon links.
 *
 * @param string $class Wrapper class.
 */
function qaf_social( $class = 'qaf-social' ) {
	$items = array(
		'instagram' => qaf_opt( 's_instagram' ),
		'facebook'  => qaf_opt( 's_facebook' ),
		'youtube'   => qaf_opt( 's_youtube' ),
		'tiktok'    => qaf_opt( 's_tiktok' ),
		'whatsapp'  => qaf_opt( 's_whatsapp' ) ? 'https://wa.me/' . preg_replace( '/\D/', '', (string) qaf_opt( 's_whatsapp' ) ) : '',
	);
	$items = array_filter( $items );
	if ( ! $items ) {
		return;
	}
	echo '<div class="' . esc_attr( $class ) . '">';
	foreach ( $items as $network => $url ) {
		printf(
			'<a href="%1$s" target="_blank" rel="noopener noreferrer" aria-label="%2$s" data-qaf-nofx>%3$s</a>',
			esc_url( $url ),
			esc_attr( ucfirst( $network ) ),
			qaf_icon( $network, 16 ) // phpcs:ignore WordPress.Security.EscapeOutput
		);
	}
	echo '</div>';
}

/**
 * tel: href.
 *
 * @param string $phone Phone.
 * @return string
 */
function qaf_tel( $phone ) {
	return 'tel:' . preg_replace( '/[^\d+]/', '', (string) $phone );
}

/**
 * Brand colour for a company.
 *
 * @param int $post_id Company.
 * @return string
 */
function qaf_company_color( $post_id ) {
	$color = sanitize_hex_color( qaf_meta( 'color', $post_id ) );
	if ( $color ) {
		return $color;
	}
	$terms = get_the_terms( $post_id, 'qaf_brand' );
	$slug  = $terms && ! is_wp_error( $terms ) ? $terms[0]->slug : '';
	return (string) qaf_opt( 'c_b_' . $slug, qaf_opt( 'c_primary' ) );
}

/**
 * Archive link for a CPT in the current language.
 *
 * @param string $type Post type.
 * @return string
 */
function qaf_archive_url( $type ) {
	$url = get_post_type_archive_link( $type );
	return $url ? (string) apply_filters( 'wpml_permalink', $url, qaf_current_lang() ) : '';
}

/**
 * Blog page URL.
 *
 * @return string
 */
function qaf_blog_url() {
	$page = (int) get_option( 'page_for_posts' );
	if ( $page ) {
		$page = (int) apply_filters( 'wpml_object_id', $page, 'page', true );
		return (string) get_permalink( $page );
	}
	return home_url( '/' );
}

/**
 * Is this page built with Elementor?
 *
 * @param int $post_id Post.
 * @return bool
 */
function qaf_is_elementor( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_queried_object_id();
	return $post_id && did_action( 'elementor/loaded' ) && 'builder' === get_post_meta( $post_id, '_elementor_edit_mode', true );
}

/* ================================================= Banner */

/**
 * Should the current view show the page banner?
 *
 * @return bool
 */
function qaf_has_banner() {
	if ( ! qaf_opt( 'banner_on' ) || is_front_page() || is_404() ) {
		return false;
	}
	/* These views print their own designed hero. */
	if ( is_singular( array( 'qaf_company', 'qaf_service', 'qaf_job', 'post' ) ) || is_post_type_archive( array( 'qaf_company', 'qaf_service', 'qaf_branch', 'qaf_job' ) ) || is_home() || is_category() || is_tag() || is_search() ) {
		return false;
	}
	if ( is_singular() ) {
		$id = get_queried_object_id();
		if ( qaf_meta( 'banner_off', $id ) || 'elementor_canvas' === get_page_template_slug( $id ) || qaf_page_recipe( $id ) ) {
			return false;
		}
		/* Elementor pages that start with a QB page hero don't need the theme banner. */
		if ( false !== strpos( (string) get_post_meta( $id, '_elementor_data', true ), '"widgetType":"qb-page-hero"' ) ) {
			return false;
		}
	}
	return (bool) apply_filters( 'qaf_has_banner', true );
}

/**
 * Banner image: post meta → featured → default.
 *
 * @return string URL.
 */
function qaf_banner_image() {
	if ( is_singular() ) {
		$id   = get_queried_object_id();
		$meta = qaf_meta( 'banner_image', $id );
		if ( $meta ) {
			return qaf_resolve_url( $meta );
		}
		if ( has_post_thumbnail( $id ) ) {
			return (string) get_the_post_thumbnail_url( $id, 'qaf-banner' );
		}
	}
	return qaf_opt_url( 'banner_image' );
}

/**
 * Banner title.
 *
 * @return string
 */
function qaf_banner_title() {
	if ( is_singular() ) {
		$custom = qaf_meta( 'banner_title', get_queried_object_id() );
		return $custom ? $custom : single_post_title( '', false );
	}
	if ( is_home() ) {
		return get_the_title( (int) get_option( 'page_for_posts' ) );
	}
	if ( is_search() ) {
		/* translators: search query */
		return sprintf( '“%s”', get_search_query() );
	}
	if ( is_post_type_archive() ) {
		return post_type_archive_title( '', false );
	}
	return wp_strip_all_tags( get_the_archive_title() );
}

/**
 * Banner eyebrow.
 *
 * @return string
 */
function qaf_banner_subtitle() {
	if ( is_singular() ) {
		$custom = qaf_meta( 'banner_sub', get_queried_object_id() );
		if ( $custom ) {
			return $custom;
		}
		if ( is_singular( 'qaf_company' ) ) {
			return trim( qaf_meta( 'en_name' ) . ' — ' . qaf_meta( 'sector' ), ' —' );
		}
	}
	return (string) qaf_opt_i18n( 'brand_sub' );
}

/**
 * Numbered pagination.
 */
function qaf_pagination() {
	$links = paginate_links(
		array(
			'type'      => 'array',
			'mid_size'  => 1,
			'prev_text' => qaf_icon( 'arrow', 16, 'qaf-flip' ) . '<span>' . qaf_text( 'str_prev' ) . '</span>',
			'next_text' => '<span>' . qaf_text( 'str_next' ) . '</span>' . qaf_icon( 'arrow', 16 ),
		)
	);
	if ( ! $links ) {
		return;
	}
	echo '<nav class="qaf-pagination" aria-label="pagination">' . implode( '', $links ) . '</nav>'; // phpcs:ignore WordPress.Security.EscapeOutput
}

/**
 * Brand chips for a post (colour per brand).
 *
 * @param int    $post_id Post.
 * @param string $class   Wrapper class.
 */
function qaf_brand_tags( $post_id, $class = 'qaf-tags' ) {
	$terms = get_the_terms( $post_id, 'qaf_brand' );
	if ( ! $terms || is_wp_error( $terms ) ) {
		return;
	}
	echo '<span class="' . esc_attr( $class ) . '">';
	foreach ( $terms as $term ) {
		printf( '<span class="qaf-tag qaf-tag--%1$s">%2$s</span>', esc_attr( qaf_brand_base_slug( $term->slug ) ), esc_html( $term->name ) );
	}
	echo '</span>';
}

/**
 * Brand slug without the language suffix the importer adds (qafesheh-en → qafesheh).
 *
 * @param string $slug Slug.
 * @return string
 */
function qaf_brand_base_slug( $slug ) {
	return preg_replace( '/-(en|ar)$/', '', (string) $slug );
}

/**
 * Brand terms in the current language, ordered like the companies.
 *
 * @return WP_Term[]
 */
function qaf_brand_terms() {
	$terms = get_terms( array( 'taxonomy' => 'qaf_brand', 'hide_empty' => false ) );
	if ( is_wp_error( $terms ) ) {
		return array();
	}
	$order = array_keys( qaf_seed_brands() );
	usort(
		$terms,
		static function ( $a, $b ) use ( $order ) {
			$ia = array_search( qaf_brand_base_slug( $a->slug ), $order, true );
			$ib = array_search( qaf_brand_base_slug( $b->slug ), $order, true );
			return ( false === $ia ? 99 : $ia ) <=> ( false === $ib ? 99 : $ib );
		}
	);
	return $terms;
}

/**
 * Google Maps embed URL for a branch.
 *
 * @param int $post_id Branch.
 * @return string
 */
function qaf_branch_embed( $post_id ) {
	$lat = qaf_meta( 'lat', $post_id );
	$lng = qaf_meta( 'lng', $post_id );
	$q   = ( $lat && $lng ) ? $lat . ',' . $lng : qaf_meta( 'address', $post_id ) . ', Jordan';
	return 'https://maps.google.com/maps?q=' . rawurlencode( $q ) . '&z=15&output=embed&hl=' . rawurlencode( qaf_current_lang() );
}

/**
 * Printf-style translated string option (%s placeholder).
 *
 * @param string $key   Option.
 * @param string $value Replacement.
 * @return string
 */
function qaf_text_f( $key, $value ) {
	$pattern = (string) qaf_opt_i18n( $key );
	return esc_html( false !== strpos( $pattern, '%s' ) ? str_replace( '%s', $value, $pattern ) : $pattern );
}

/**
 * A brand colour that stays readable on light surfaces (very light identities fall back to ink).
 *
 * @param string $hex Colour.
 * @return string
 */
function qaf_color_on_light( $hex ) {
	$hex = sanitize_hex_color( $hex );
	if ( ! $hex ) {
		return (string) qaf_opt( 'c_ink' );
	}
	list( $r, $g, $b ) = array_map( 'intval', explode( ',', qaf_hex_rgb( $hex ) ) );
	$lum = ( 0.2126 * $r + 0.7152 * $g + 0.0722 * $b ) / 255;
	return $lum > 0.62 ? (string) qaf_opt( 'c_ink' ) : $hex;
}

/**
 * Floating-label input for the application form.
 *
 * @param string $uid   Form id.
 * @param string $name  Field name.
 * @param string $type  Input type.
 * @param string $label Label option key.
 * @param array  $attrs Attributes.
 */
function qaf_apply_field( $uid, $name, $type, $label, $attrs = array() ) {
	$id   = $uid . '-' . str_replace( 'qaf_', '', $name );
	$html = '';
	foreach ( $attrs as $k => $v ) {
		$html .= ' ' . esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
	}
	printf(
		'<label class="qaf-field" for="%1$s"><input id="%1$s" type="%2$s" name="%3$s" placeholder=" "%4$s><span class="qaf-field__label">%5$s</span><span class="qaf-field__error" data-for="%3$s"></span></label>',
		esc_attr( $id ),
		esc_attr( $type ),
		esc_attr( $name ),
		$html, // phpcs:ignore WordPress.Security.EscapeOutput
		qaf_text( $label ) // phpcs:ignore WordPress.Security.EscapeOutput
	);
}

/**
 * Open a site form (contact / complaint): wrapper, hidden fields, nonce, honeypot, status.
 *
 * @param string $form_id Form.
 * @param string $class   Extra class.
 */
function qaf_form_open( $form_id, $class = '' ) {
	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	$sent     = isset( $_GET['qaf-sent'] );
	$error    = isset( $_GET['qaf-form-error'] );
	$tracking = isset( $_GET['qaf-tracking'] ) ? preg_replace( '/[^A-Z0-9\-]/', '', strtoupper( sanitize_text_field( wp_unslash( $_GET['qaf-tracking'] ) ) ) ) : '';
	// phpcs:enable
	$success = (string) qaf_opt_i18n( 'complaint' === $form_id ? 'form_complaint_success' : 'form_contact_success' );
	printf(
		'<form class="qaf-apply qaf-apply--light qaf-sform %1$s" id="qaf-form" method="post" action="%2$s" enctype="multipart/form-data" novalidate data-qaf-form data-max="%3$d" data-required="%4$s" data-bad-email="%5$s" data-bad-file="%6$s" data-sending="%7$s" data-error="%8$s">',
		esc_attr( $class ),
		esc_url( admin_url( 'admin-post.php' ) ),
		5 * MB_IN_BYTES,
		esc_attr( qaf_opt_i18n( 'str_f_required' ) ),
		esc_attr( qaf_opt_i18n( 'str_f_bad_email' ) ),
		esc_attr( str_replace( '%s', '5', (string) qaf_opt_i18n( 'str_f_bad_attach' ) ) ),
		esc_attr( qaf_opt_i18n( 'str_f_sending' ) ),
		esc_attr( qaf_opt_i18n( 'str_f_error' ) )
	);
	$GLOBALS['qaf_open_form'] = $form_id;
	echo '<input type="hidden" name="action" value="qaf_form">';
	printf( '<input type="hidden" name="qaf_form" value="%s">', esc_attr( $form_id ) );
	printf( '<input type="hidden" name="qaf_t" value="%d">', time() );
	wp_nonce_field( 'qaf_form_' . $form_id, 'qaf_form_nonce' );
	echo '<div class="qaf-apply__hp" aria-hidden="true"><label>Website<input type="text" name="qaf_website" tabindex="-1" autocomplete="off"></label></div>';
	printf(
		'<div class="qaf-apply__status%1$s" role="status" aria-live="polite"%2$s>%3$s</div>',
		$error ? ' is-error' : '',
		( $sent || $error ) ? '' : ' hidden',
		esc_html( $sent ? str_replace( '{tracking}', $tracking, $success ) : ( $error ? (string) qaf_opt_i18n( 'str_f_error' ) : '' ) )
	);
}

/**
 * Select with placeholder label.
 *
 * @param string $name    Field name.
 * @param string $label   Label option key.
 * @param array  $options value => [ label, data ].
 * @param string $data    Data attribute name for extra data.
 */
function qaf_form_select( $name, $label, $options, $data = '' ) {
	$id = 'qaf-f-' . str_replace( 'qaf_', '', $name );
	printf( '<label class="qaf-field qaf-field--select" for="%1$s"><span class="screen-reader-text">%2$s</span><select id="%1$s" name="%3$s"><option value="">%2$s</option>', esc_attr( $id ), qaf_text( $label ), esc_attr( $name ) ); // phpcs:ignore WordPress.Security.EscapeOutput
	foreach ( $options as $value => $opt ) {
		printf( '<option value="%1$s"%3$s>%2$s</option>', esc_attr( (string) $value ), esc_html( $opt[0] ), ( $data && '' !== $opt[1] ) ? ' data-' . esc_attr( $data ) . '="' . esc_attr( $opt[1] ) . '"' : '' );
	}
	printf( '</select><span class="qaf-field__error" data-for="%s"></span></label>', esc_attr( $name ) );
}

/**
 * Textarea with floating label.
 *
 * @param string $name  Name.
 * @param string $label Label option key.
 * @param int    $rows  Rows.
 */
function qaf_form_textarea( $name, $label, $rows = 6 ) {
	$id = 'qaf-f-' . str_replace( 'qaf_', '', $name );
	printf( '<label class="qaf-field qaf-field--area" for="%1$s"><span class="qaf-field__label">%2$s</span><textarea id="%1$s" name="%3$s" rows="%4$d" maxlength="4000" required></textarea><span class="qaf-field__error" data-for="%3$s"></span></label>', esc_attr( $id ), qaf_text( $label ), esc_attr( $name ), absint( $rows ) ); // phpcs:ignore WordPress.Security.EscapeOutput
}

/**
 * Submit row.
 *
 * @param string $note Note option key.
 */
function qaf_form_close( $note ) {
	if ( function_exists( 'qb_captcha_field' ) ) {
		qb_captcha_field( 'form_' . (string) ( $GLOBALS['qaf_open_form'] ?? 'contact' ), array( 'class' => 'qaf-apply__captcha' ) );
	}
	printf( '<div class="qaf-apply__foot"><span class="qaf-apply__note">%s</span><button class="qaf-btn qaf-btn--solid qaf-btn--lg qaf-apply__submit" type="submit"><span class="qaf-btn__label">%s</span><span class="qaf-apply__spin" aria-hidden="true"></span></button></div></form>', qaf_text( $note ), qaf_text( 'str_send' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
}

/**
 * Section recipe for an inner page (by import key), or null.
 *
 * @param int $post_id Page.
 * @return array|null [ copy key, sections ]
 */
function qaf_page_recipe( $post_id ) {
	$key     = (string) get_post_meta( (int) $post_id, '_qaf_import_key', true );
	$recipes = qaf_page_recipes();
	return $recipes[ $key ] ?? null;
}

/**
 * Render an inner page from its recipe (used when the page is not built with Elementor).
 *
 * @param int $post_id Page.
 */
function qaf_render_page_recipe( $post_id ) {
	$recipe = qaf_page_recipe( $post_id );
	if ( ! $recipe ) {
		return;
	}
	$copy = qaf_seed_page_copy( $recipe[0] );
	foreach ( $recipe[1] as $section => $copy_key ) {
		if ( 'steps-bar' === $section ) {
			$args = array( 'items' => $copy[ $copy_key ] ?? array() );
		} else {
			$args = $copy_key ? ( $copy[ $copy_key ] ?? array() ) : array();
		}
		qaf_section( $section, $args );
	}
}

/**
 * Server-side word split for animated titles (keeps Arabic words whole; newlines → <br>).
 *
 * @param string $text Text.
 * @return string
 */
function qaf_split_words( $text ) {
	$i     = 0;
	$lines = preg_split( '/\r\n|\r|\n/', (string) $text );
	$out   = array();
	foreach ( $lines as $line ) {
		$words = preg_split( '/\s+/u', trim( $line ), -1, PREG_SPLIT_NO_EMPTY );
		$html  = array();
		foreach ( $words as $word ) {
			$html[] = '<span class="qaf-w" style="--qaf-i:' . ( $i++ ) . '">' . esc_html( $word ) . '</span>';
		}
		$out[] = implode( ' ', $html );
	}
	return implode( '<br>', $out );
}
