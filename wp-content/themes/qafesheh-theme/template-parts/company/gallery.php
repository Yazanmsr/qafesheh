<?php
/**
 * Company gallery: tall arched image + two stacked columns.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$qaf_ids = array_slice( qaf_meta_gallery(), 0, 5 );
if ( ! $qaf_ids ) {
	return;
}
$qaf_first = array_shift( $qaf_ids );
$qaf_cols  = array_chunk( $qaf_ids, 2 );
?>
<section class="qaf-cogallery">
	<div class="qaf-container qaf-cogallery__grid qaf-cogallery__grid--<?php echo absint( count( $qaf_cols ) ); ?>" data-qaf-stagger>
		<figure class="qaf-cogallery__main qaf-arch"<?php echo qaf_anim( 'reveal' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
			<div class="qaf-cogallery__img"><?php echo wp_get_attachment_image( $qaf_first, 'qaf-wide', false, array( 'sizes' => '(max-width: 900px) 100vw, 50vw', 'loading' => 'lazy' ) ); ?></div>
		</figure>
		<?php foreach ( $qaf_cols as $qaf_col ) : ?>
			<div class="qaf-cogallery__col">
				<?php foreach ( $qaf_col as $qaf_img ) : ?>
					<figure class="qaf-cogallery__item"<?php echo qaf_anim( 'reveal' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
						<div class="qaf-cogallery__img"><?php echo wp_get_attachment_image( $qaf_img, 'qaf-thumb', false, array( 'sizes' => '(max-width: 900px) 50vw, 25vw', 'loading' => 'lazy' ) ); ?></div>
					</figure>
				<?php endforeach; ?>
			</div>
		<?php endforeach; ?>
	</div>
</section>
