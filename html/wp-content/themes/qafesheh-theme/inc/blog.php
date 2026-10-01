<?php
/**
 * Articles helpers: reading time, heading anchors + table of contents, featured post, related, share.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

/**
 * Reading minutes (Arabic reads a little slower than English).
 *
 * @param int $post_id Post.
 * @return int
 */
function qaf_reading_minutes( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$text    = wp_strip_all_tags( (string) get_post_field( 'post_content', $post_id ) );
	$words   = count( preg_split( '/\s+/u', trim( $text ), -1, PREG_SPLIT_NO_EMPTY ) );
	$wpm     = 'ar' === qaf_current_lang() ? 180 : 220;
	return max( 1, (int) ceil( $words / $wpm ) );
}

/**
 * Add stable ids to h2/h3 inside article content (used by the table of contents).
 *
 * @param string $content Content.
 * @return string
 */
function qaf_heading_anchors( $content ) {
	if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}
	$used = array();
	return preg_replace_callback(
		'#<h([23])([^>]*)>(.*?)</h\1>#is',
		static function ( $m ) use ( &$used ) {
			if ( preg_match( '/\sid=["\']/', $m[2] ) ) {
				return $m[0];
			}
			$base = sanitize_title( wp_strip_all_tags( $m[3] ) );
			$base = $base ? 'sec-' . substr( md5( $base ), 0, 6 ) : 'sec';
			$id   = $base;
			$n    = 2;
			while ( isset( $used[ $id ] ) ) {
				$id = $base . '-' . $n++;
			}
			$used[ $id ] = true;
			return sprintf( '<h%1$s id="%2$s"%3$s>%4$s</h%1$s>', $m[1], esc_attr( $id ), $m[2], $m[3] );
		},
		$content
	);
}
add_filter( 'the_content', 'qaf_heading_anchors', 20 );

/**
 * Table of contents from rendered content.
 *
 * @param string $html Rendered content (after qaf_heading_anchors).
 * @return array[] [ id, text, level ]
 */
function qaf_toc_items( $html ) {
	$items = array();
	if ( preg_match_all( '#<h([23])[^>]*\sid="([^"]+)"[^>]*>(.*?)</h\1>#is', $html, $m, PREG_SET_ORDER ) ) {
		foreach ( $m as $h ) {
			$items[] = array( 'id' => $h[2], 'text' => wp_strip_all_tags( $h[3] ), 'level' => (int) $h[1] );
		}
	}
	return $items;
}

/**
 * Featured post id for the blog home (sticky first, else latest).
 *
 * @return int
 */
function qaf_featured_post_id() {
	static $id = null;
	if ( null !== $id ) {
		return $id;
	}
	$id     = 0;
	$sticky = array_filter( array_map( 'absint', (array) get_option( 'sticky_posts' ) ) );
	$args   = array( 'post_type' => 'post', 'posts_per_page' => 1, 'fields' => 'ids', 'ignore_sticky_posts' => true, 'suppress_filters' => false );
	if ( $sticky ) {
		$args['post__in'] = $sticky;
	}
	$found = get_posts( $args );
	if ( ! $found && $sticky ) {
		unset( $args['post__in'] );
		$found = get_posts( $args );
	}
	$id = $found ? (int) $found[0] : 0;
	return $id;
}

/**
 * Should the blog home show the featured block?
 *
 * @return bool
 */
function qaf_blog_has_featured() {
	return is_home() && ! is_paged() && qaf_opt( 'blog_featured' ) && qaf_featured_post_id();
}

/**
 * Exclude the featured post from the first page grid (and keep pagination consistent).
 *
 * @param WP_Query $q Query.
 */
function qaf_blog_exclude_featured( $q ) {
	if ( is_admin() || ! $q->is_main_query() || ! $q->is_home() || ! qaf_opt( 'blog_featured' ) ) {
		return;
	}
	$q->set( 'ignore_sticky_posts', true );
	remove_action( 'pre_get_posts', 'qaf_blog_exclude_featured', 20 );
	$featured = qaf_featured_post_id();
	add_action( 'pre_get_posts', 'qaf_blog_exclude_featured', 20 );
	if ( $featured ) {
		$q->set( 'post__not_in', array( $featured ) );
	}
}
add_action( 'pre_get_posts', 'qaf_blog_exclude_featured', 20 );

/**
 * Related posts: same category first, then latest.
 *
 * @param int $post_id Post.
 * @param int $count   Count.
 * @return WP_Post[]
 */
function qaf_related_posts( $post_id, $count = 3 ) {
	$cats  = wp_get_post_categories( $post_id );
	$posts = $cats ? get_posts( array( 'post_type' => 'post', 'posts_per_page' => $count, 'post__not_in' => array( $post_id ), 'category__in' => $cats, 'ignore_sticky_posts' => true, 'suppress_filters' => false ) ) : array();
	if ( count( $posts ) < $count ) {
		$exclude = array_merge( array( $post_id ), wp_list_pluck( $posts, 'ID' ) );
		$posts   = array_merge( $posts, get_posts( array( 'post_type' => 'post', 'posts_per_page' => $count - count( $posts ), 'post__not_in' => $exclude, 'ignore_sticky_posts' => true, 'suppress_filters' => false ) ) );
	}
	return $posts;
}

/**
 * Share targets for the current post.
 *
 * @return array[]
 */
function qaf_share_links() {
	$url   = rawurlencode( get_permalink() );
	$title = rawurlencode( wp_strip_all_tags( get_the_title() ) );
	$ar = 'ar' === qaf_current_lang();
	return array(
		array( 'facebook', $ar ? 'فيسبوك' : 'Facebook', 'https://www.facebook.com/sharer/sharer.php?u=' . $url ),
		array( 'x', $ar ? 'إكس' : 'X', 'https://twitter.com/intent/tweet?url=' . $url . '&text=' . $title ),
		array( 'whatsapp', $ar ? 'واتساب' : 'WhatsApp', 'https://wa.me/?text=' . $title . '%20' . $url ),
	);
}

/**
 * Primary category of a post.
 *
 * @param int $post_id Post.
 * @return WP_Term|null
 */
function qaf_primary_category( $post_id ) {
	$cats = get_the_category( $post_id );
	return $cats ? $cats[0] : null;
}

/**
 * Meta line: category ✦ date [✦ minutes].
 *
 * @param int  $post_id Post.
 * @param bool $minutes Include reading time.
 */
function qaf_post_meta_line( $post_id, $minutes = false ) {
	$cat   = qaf_primary_category( $post_id );
	$parts = array();
	if ( $cat ) {
		$parts[] = '<span>' . esc_html( $cat->name ) . '</span>';
	}
	$parts[] = '<time datetime="' . esc_attr( get_the_date( 'c', $post_id ) ) . '">' . esc_html( get_the_date( '', $post_id ) ) . '</time>';
	if ( $minutes ) {
		$parts[] = '<span>' . qaf_text_f( 'str_minutes', (string) qaf_reading_minutes( $post_id ) ) . '</span>';
	}
	echo '<p class="qaf-pmeta">' . implode( '<span class="qaf-pmeta__sep" aria-hidden="true">✦</span>', $parts ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput
}
