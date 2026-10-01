<?php
/**
 * 01 — Group statement.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$a = wp_parse_args( $args ?? array(), qaf_seed_home()['statement'] + array( 'id' => '' ) );
?>
<section class="qaf-sec qaf-statement"<?php echo $a['id'] ? ' id="' . esc_attr( $a['id'] ) . '"' : ''; ?>>
	<div class="qaf-container qaf-chapter">
		<?php qaf_chapter_rail( $a['num'], $a['label'] ); ?>
		<div class="qaf-chapter__body">
			<?php if ( $a['title'] ) : ?>
				<h2 class="qaf-statement__title" data-qaf-anim="words"><?php echo qaf_lines( $a['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
			<?php endif; ?>
			<?php if ( $a['col1'] || $a['col2'] ) : ?>
				<div class="qaf-statement__cols" data-qaf-stagger>
					<?php if ( $a['col1'] ) : ?>
						<p<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo esc_html( $a['col1'] ); ?></p>
					<?php endif; ?>
					<?php if ( $a['col2'] ) : ?>
						<p<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo esc_html( $a['col2'] ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<div<?php echo qaf_anim( 'up', 120 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php qaf_arrow_link( $a['link'], $a['link_url'] ); ?></div>
		</div>
	</div>
</section>
