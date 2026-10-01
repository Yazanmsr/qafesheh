<?php
/**
 * Single entry body.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;
?>
<article <?php post_class( 'qaf-sec qaf-single' ); ?>>
	<div class="qaf-container qaf-single__in">
		<?php if ( 'post' === get_post_type() ) : ?>
			<p class="qaf-single__meta"<?php echo qaf_anim( 'fade' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>" dir="ltr"><?php echo esc_html( get_the_date( 'd.m.Y' ) ); ?></time> — <?php echo wp_kses_post( get_the_category_list( ' · ' ) ); ?></p>
		<?php endif; ?>
		<div class="qaf-prose"<?php echo qaf_anim( 'up', 80 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
			<?php
			the_content();
			wp_link_pages();
			?>
		</div>
		<?php get_template_part( 'template-parts/content/post', 'nav' ); ?>
	</div>
</article>
