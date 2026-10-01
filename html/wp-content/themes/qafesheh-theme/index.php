<?php
/**
 * Fallback archive / blog index.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="qaf-sec qaf-archive">
	<div class="qaf-container">
		<?php if ( have_posts() ) : ?>
			<div class="qaf-cards" data-qaf-stagger>
				<?php
				while ( have_posts() ) {
					the_post();
					get_template_part( 'template-parts/content/card', 'post' );
				}
				?>
			</div>
			<?php qaf_pagination(); ?>
		<?php else : ?>
			<?php get_template_part( 'template-parts/content/content', 'none' ); ?>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
