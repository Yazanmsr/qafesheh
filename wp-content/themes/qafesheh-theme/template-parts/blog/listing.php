<?php
/**
 * Articles listing: hero, category bar + search, featured article, arched grid, pagination.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

if ( is_category() || is_tag() ) {
	$qaf_title = single_term_title( '', false );
	$qaf_text  = wp_strip_all_tags( term_description() );
} elseif ( is_search() ) {
	$qaf_title = sprintf( '“%s”', get_search_query() );
	$qaf_text  = '';
} else {
	$qaf_title = qaf_opt_i18n( 'blog_title' );
	$qaf_text  = qaf_opt_i18n( 'blog_text' );
}

get_template_part( 'template-parts/header/page-hero', null, array( 'variant' => 'stack', 'title' => $qaf_title, 'text' => $qaf_text ) );
get_template_part( 'template-parts/blog/catbar' );

if ( qaf_blog_has_featured() ) {
	get_template_part( 'template-parts/blog/featured', null, array( 'post_id' => qaf_featured_post_id() ) );
}
?>
<section class="qaf-sec qaf-blog<?php echo qaf_blog_has_featured() ? ' qaf-blog--after-featured' : ''; ?>">
	<div class="qaf-container">
		<?php if ( have_posts() ) : ?>
			<div class="qaf-agrid" data-qaf-stagger>
				<?php
				while ( have_posts() ) {
					the_post();
					get_template_part( 'template-parts/blog/card' );
				}
				?>
			</div>
			<?php qaf_pagination(); ?>
		<?php elseif ( ! qaf_blog_has_featured() ) : ?>
			<?php get_template_part( 'template-parts/content/content', 'none' ); ?>
		<?php endif; ?>
	</div>
</section>
