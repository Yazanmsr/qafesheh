<?php
/**
 * Closing CTA band.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$a = wp_parse_args( $args ?? array(), qaf_seed_home()['cta'] + array( 'id' => '' ) );
?>
<section class="qaf-cta"<?php echo $a['id'] ? ' id="' . esc_attr( $a['id'] ) . '"' : ''; ?>>
	<div class="qaf-lines" aria-hidden="true"></div>
	<div class="qaf-container qaf-cta__in">
		<div class="qaf-cta__copy">
			<span class="qaf-rule qaf-rule--lg" aria-hidden="true"<?php echo qaf_anim( 'line' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>></span>
			<?php if ( $a['title'] ) : ?>
				<h2 class="qaf-cta__title" data-qaf-anim="words"><?php echo qaf_lines( $a['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
			<?php endif; ?>
			<?php if ( $a['text'] ) : ?>
				<p class="qaf-cta__text"<?php echo qaf_anim( 'up', 160 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo esc_html( $a['text'] ); ?></p>
			<?php endif; ?>
		</div>
		<div class="qaf-cta__actions"<?php echo qaf_anim( 'up', 240 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
			<?php qaf_button( $a['btn1'], $a['btn1_url'], 'solid', 'qaf-btn--lg' ); ?>
			<?php qaf_button( $a['btn2'], $a['btn2_url'], 'outline', 'qaf-btn--lg' ); ?>
		</div>
	</div>
</section>
