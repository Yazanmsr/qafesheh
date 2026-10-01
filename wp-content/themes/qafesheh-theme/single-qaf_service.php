<?php
/**
 * Service page: media hero with icon, body (intro, includes, images, steps) + sidebar.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) {
	the_post();

	get_template_part(
		'template-parts/header/page-hero',
		null,
		array(
			'variant'  => 'media',
			'image'    => get_post_thumbnail_id(),
			'position' => 'left center',
			'icon'     => qaf_meta( 'icon' ) ? qaf_meta( 'icon' ) : 'star',
			'title'    => get_the_title(),
			'text'     => wp_strip_all_tags( get_the_excerpt() ),
		)
	);
	?>
	<section class="qaf-sec qaf-svcsingle">
		<div class="qaf-container qaf-svcsingle__grid">
			<div class="qaf-svcsingle__body">
				<?php get_template_part( 'template-parts/services/body' ); ?>
			</div>
			<aside class="qaf-svcsingle__side">
				<?php get_template_part( 'template-parts/services/sidebar' ); ?>
			</aside>
		</div>
	</section>
	<?php
}

get_footer();
