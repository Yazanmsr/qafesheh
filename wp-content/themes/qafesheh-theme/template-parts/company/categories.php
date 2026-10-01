<?php
/**
 * Company categories: “What we make”.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$qaf_rows = array_filter( qaf_meta_rows( 'categories' ), static fn( $r ) => ! empty( $r['name'] ) );
if ( ! $qaf_rows ) {
	return;
}
?>
<section class="qaf-sec qaf-cocats">
	<span class="qaf-cocats__ghost" aria-hidden="true">01</span>
	<div class="qaf-container">
		<div class="qaf-cocats__head">
			<p class="qaf-kicker"<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><span class="qaf-kicker__rule" aria-hidden="true"></span><?php echo qaf_text( 'str_cats' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
			<h2 class="qaf-cocats__title" data-qaf-anim="words"><?php echo qaf_text( 'str_cats_title' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
		</div>
		<div class="qaf-cocats__grid" data-qaf-stagger>
			<?php foreach ( array_values( $qaf_rows ) as $qaf_i => $qaf_row ) : ?>
				<?php
				$qaf_url = ! empty( $qaf_row['url'] ) ? $qaf_row['url'] : '';
				$qaf_tag = $qaf_url ? 'a' : 'div';
				?>
				<<?php echo $qaf_tag; // phpcs:ignore WordPress.Security.EscapeOutput ?> class="qaf-cocat"<?php echo $qaf_url ? ' href="' . esc_url( $qaf_url ) . '"' : ''; ?><?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
					<span class="qaf-cocat__media"><?php echo qaf_image( $qaf_row['image'] ?? '', 'qaf-thumb', array( 'sizes' => '(max-width: 700px) 50vw, 25vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<span class="qaf-cocat__foot">
						<span class="qaf-cocat__name"><?php echo esc_html( $qaf_row['name'] ); ?></span>
						<span class="qaf-cocat__num"><?php echo esc_html( str_pad( (string) ( $qaf_i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
					</span>
				</<?php echo $qaf_tag; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
			<?php endforeach; ?>
		</div>
	</div>
</section>
