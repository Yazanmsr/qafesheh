<?php
/**
 * 05 — Standards (dark rows).
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$a        = wp_parse_args( $args ?? array(), qaf_seed_home()['standards'] + array( 'id' => '' ) );
$qaf_rows = array_filter( (array) $a['rows'], static fn( $r ) => ! empty( $r['title'] ) );
?>
<section class="qaf-sec qaf-sec--ink2 qaf-standards"<?php echo $a['id'] ? ' id="' . esc_attr( $a['id'] ) . '"' : ''; ?>>
	<div class="qaf-lines" aria-hidden="true"></div>
	<div class="qaf-container qaf-chapter">
		<?php qaf_chapter_rail( $a['num'], $a['label'] ); ?>
		<div class="qaf-chapter__body">
			<div class="qaf-rows" data-qaf-stagger>
				<?php foreach ( $qaf_rows as $qaf_row ) : ?>
					<div class="qaf-rows__item"<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
						<h3 class="qaf-rows__title"><?php echo esc_html( $qaf_row['title'] ); ?></h3>
						<p class="qaf-rows__text"><?php echo esc_html( $qaf_row['text'] ?? '' ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
