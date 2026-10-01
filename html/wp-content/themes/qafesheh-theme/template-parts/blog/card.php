<?php
/**
 * Article card (arched image).
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;
?>
<article <?php post_class( 'qaf-acard' ); ?><?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<a class="qaf-acard__link" href="<?php the_permalink(); ?>">
		<span class="qaf-acard__media qaf-arch">
			<span class="qaf-acard__img">
				<?php if ( has_post_thumbnail() ) : ?>
					<?php the_post_thumbnail( 'qaf-thumb', array( 'sizes' => '(max-width: 700px) 100vw, (max-width: 1100px) 50vw, 33vw', 'alt' => '' ) ); ?>
				<?php endif; ?>
			</span>
		</span>
		<?php qaf_post_meta_line( get_the_ID() ); ?>
		<h2 class="qaf-acard__title"><?php the_title(); ?></h2>
		<p class="qaf-acard__text"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( get_the_excerpt() ), 20, '…' ) ); ?></p>
	</a>
</article>
