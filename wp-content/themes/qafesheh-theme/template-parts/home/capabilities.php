<?php
/**
 * 03 — Manufacturing capabilities: framed image + spec table + two thumbnails.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$a = wp_parse_args(
	$args ?? array(),
	qaf_seed_home()['capabilities'] + array(
		'image'  => 'factory-women.jpg',
		'thumb1' => 'boutique-coats.jpg',
		'thumb2' => 'textiles-stack.jpg',
		'id'     => '',
	)
);
$qaf_rows = array_filter( (array) $a['rows'], static fn( $r ) => ! empty( $r['label'] ) || ! empty( $r['value'] ) );
?>
<section class="qaf-sec qaf-sec--soft qaf-caps"<?php echo $a['id'] ? ' id="' . esc_attr( $a['id'] ) . '"' : ''; ?>>
	<div class="qaf-container qaf-chapter">
		<?php qaf_chapter_rail( $a['num'], $a['label'] ); ?>
		<div class="qaf-chapter__body">
			<?php if ( $a['title'] ) : ?>
				<h2 class="qaf-h2" data-qaf-anim="words"><?php echo qaf_lines( $a['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
			<?php endif; ?>

			<div class="qaf-caps__grid">
				<?php if ( $a['image'] ) : ?>
					<figure class="qaf-frame qaf-caps__main"<?php echo qaf_anim( 'reveal' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
						<div class="qaf-frame__media">
							<?php echo qaf_image( $a['image'], 'qaf-wide', array( 'sizes' => '(max-width: 1024px) 100vw, 45vw', 'style' => 'object-position:left center' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						</div>
					</figure>
				<?php endif; ?>

				<div class="qaf-caps__side">
					<?php if ( $qaf_rows ) : ?>
						<dl class="qaf-specs qaf-specs--wide" data-qaf-stagger>
							<?php foreach ( $qaf_rows as $qaf_row ) : ?>
								<div class="qaf-specs__row"<?php echo qaf_anim( 'start' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
									<dt><?php echo esc_html( $qaf_row['label'] ?? '' ); ?></dt>
									<dd><?php echo esc_html( $qaf_row['value'] ?? '' ); ?></dd>
								</div>
							<?php endforeach; ?>
						</dl>
					<?php endif; ?>

					<?php if ( $a['thumb1'] || $a['thumb2'] ) : ?>
						<div class="qaf-caps__thumbs" data-qaf-stagger>
							<?php foreach ( array( $a['thumb1'], $a['thumb2'] ) as $qaf_t ) : ?>
								<?php if ( $qaf_t ) : ?>
									<figure class="qaf-frame qaf-frame--sm"<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
										<div class="qaf-frame__media"><?php echo qaf_image( $qaf_t, 'qaf-thumb', array( 'sizes' => '(max-width: 1024px) 50vw, 22vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
									</figure>
								<?php endif; ?>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>
