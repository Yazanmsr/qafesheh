<?php
/**
 * One service row: number + icon | title, text, link | arched image.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$qaf_index = absint( $args['index'] ?? 1 );
$qaf_icon  = qaf_meta( 'icon' ) ? qaf_meta( 'icon' ) : 'star';
?>
<li class="qaf-svcrow">
	<a class="qaf-svcrow__link" href="<?php the_permalink(); ?>">
		<span class="qaf-svcrow__side"<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
			<span class="qaf-svcrow__num" dir="ltr"><?php echo esc_html( str_pad( (string) $qaf_index, 2, '0', STR_PAD_LEFT ) ); ?></span>
			<?php echo qaf_icon( $qaf_icon, 36, 'qaf-svcrow__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</span>
		<span class="qaf-svcrow__main"<?php echo qaf_anim( 'up', 90 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
			<span class="qaf-svcrow__title"><?php the_title(); ?></span>
			<?php if ( has_excerpt() ) : ?>
				<span class="qaf-svcrow__text"><?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?></span>
			<?php endif; ?>
			<span class="qaf-svcrow__more"><span><?php echo qaf_text( 'str_svc_details' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span><?php echo qaf_icon( 'arrow', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		</span>
		<?php if ( has_post_thumbnail() ) : ?>
			<span class="qaf-svcrow__media qaf-arch qaf-arch--sm"<?php echo qaf_anim( 'reveal', 160 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
				<span class="qaf-svcrow__img"><?php the_post_thumbnail( 'qaf-thumb', array( 'sizes' => '(max-width: 900px) 100vw, 380px', 'alt' => '' ) ); ?></span>
			</span>
		<?php endif; ?>
	</a>
</li>
