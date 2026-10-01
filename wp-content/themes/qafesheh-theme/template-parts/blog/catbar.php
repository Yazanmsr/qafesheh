<?php
/**
 * Category chips + article search.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$qaf_cats    = get_categories( array( 'hide_empty' => true, 'number' => 8, 'orderby' => 'count', 'order' => 'DESC' ) );
$qaf_current = is_category() ? get_queried_object_id() : 0;
?>
<div class="qaf-catbar">
	<div class="qaf-container qaf-catbar__in">
		<nav class="qaf-catbar__chips" aria-label="categories">
			<a class="qaf-chip qaf-chip--lg<?php echo ( is_home() ) ? ' is-active' : ''; ?>" href="<?php echo esc_url( qaf_blog_url() ); ?>"<?php echo is_home() ? ' aria-current="page"' : ''; ?>><?php echo qaf_text( 'str_all' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
			<?php foreach ( $qaf_cats as $qaf_cat ) : ?>
				<?php
				if ( 'uncategorized' === $qaf_cat->slug ) {
					continue;
				}
				$qaf_on = (int) $qaf_cat->term_id === (int) $qaf_current;
				?>
				<a class="qaf-chip qaf-chip--lg<?php echo $qaf_on ? ' is-active' : ''; ?>" href="<?php echo esc_url( get_category_link( $qaf_cat ) ); ?>"<?php echo $qaf_on ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $qaf_cat->name ); ?></a>
			<?php endforeach; ?>
		</nav>
		<form class="qaf-catbar__search" role="search" method="get" action="<?php echo esc_url( apply_filters( 'wpml_home_url', home_url( '/' ) ) ); ?>">
			<?php echo qaf_icon( 'search', 17 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<label class="screen-reader-text" for="qaf-post-search"><?php echo qaf_text( 'str_search_posts' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></label>
			<input id="qaf-post-search" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php echo esc_attr( qaf_opt_i18n( 'str_search_posts' ) ); ?>">
			<input type="hidden" name="post_type" value="post">
			<?php do_action( 'wpml_add_language_form_field' ); ?>
		</form>
	</div>
</div>
