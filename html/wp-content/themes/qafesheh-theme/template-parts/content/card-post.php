<?php
/**
 * Card used by archives and load-more.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;
?>
<article <?php post_class( 'qaf-card' ); ?><?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<a class="qaf-card__link" href="<?php the_permalink(); ?>">
		<figure class="qaf-frame qaf-frame--sm qaf-card__media">
			<div class="qaf-frame__media">
				<?php if ( has_post_thumbnail() ) : ?>
					<?php the_post_thumbnail( 'qaf-thumb', array( 'sizes' => '(max-width: 700px) 100vw, (max-width: 1100px) 50vw, 33vw' ) ); ?>
				<?php else : ?>
					<span class="qaf-card__ph" aria-hidden="true"></span>
				<?php endif; ?>
			</div>
		</figure>
		<div class="qaf-card__body">
			<?php if ( 'post' === get_post_type() ) : ?>
				<p class="qaf-card__meta"><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>" dir="ltr"><?php echo esc_html( get_the_date( 'd.m.Y' ) ); ?></time></p>
			<?php endif; ?>
			<h2 class="qaf-card__title"><?php the_title(); ?></h2>
			<?php if ( has_excerpt() || 'post' === get_post_type() ) : ?>
				<p class="qaf-card__text"><?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?></p>
			<?php endif; ?>
			<span class="qaf-link"><span><?php echo qaf_text( 'str_read' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span><?php echo qaf_icon( 'arrow', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		</div>
	</a>
</article>
