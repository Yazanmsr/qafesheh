<?php
/**
 * Featured article.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$qaf_id = absint( $args['post_id'] ?? 0 );
if ( ! $qaf_id ) {
	return;
}
$qaf_url = get_permalink( $qaf_id );
?>
<section class="qaf-featured">
	<div class="qaf-container qaf-featured__grid">
		<a class="qaf-featured__media" href="<?php echo esc_url( $qaf_url ); ?>" tabindex="-1" aria-hidden="true">
			<span class="qaf-featured__arch qaf-arch"<?php echo qaf_anim( 'reveal' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
				<span class="qaf-featured__img">
					<?php
					if ( has_post_thumbnail( $qaf_id ) ) {
						echo get_the_post_thumbnail( $qaf_id, 'qaf-wide', array( 'sizes' => '(max-width: 900px) 100vw, 55vw', 'loading' => 'eager', 'fetchpriority' => 'high', 'alt' => '' ) );
					}
					?>
				</span>
			</span>
			<span class="qaf-featured__badge"<?php echo qaf_anim( 'zoom', 400 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo qaf_text( 'str_featured' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		</a>
		<div class="qaf-featured__body">
			<div<?php echo qaf_anim( 'fade', 120 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php qaf_post_meta_line( $qaf_id, true ); ?></div>
			<h2 class="qaf-featured__title" data-qaf-anim="words" style="--qaf-delay:160ms"><a href="<?php echo esc_url( $qaf_url ); ?>"><?php echo esc_html( get_the_title( $qaf_id ) ); ?></a></h2>
			<span class="qaf-featured__rule" aria-hidden="true"<?php echo qaf_anim( 'line', 320 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><span></span></span>
			<p class="qaf-featured__text"<?php echo qaf_anim( 'up', 380 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo esc_html( wp_trim_words( wp_strip_all_tags( get_the_excerpt( $qaf_id ) ), 40, '…' ) ); ?></p>
			<div<?php echo qaf_anim( 'up', 460 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php qaf_arrow_link( qaf_opt_i18n( 'str_read_article' ), $qaf_url ); ?></div>
		</div>
	</div>
</section>
