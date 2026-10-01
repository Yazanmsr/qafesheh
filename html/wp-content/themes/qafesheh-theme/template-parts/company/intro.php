<?php
/**
 * Company intro: eyebrow + title | editor paragraphs.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$qaf_title   = qaf_meta( 'intro_t' );
$qaf_content = apply_filters( 'the_content', get_the_content() );
if ( ! $qaf_title && ! trim( wp_strip_all_tags( $qaf_content ) ) ) {
	return;
}
?>
<section class="qaf-sec qaf-sec--ink qaf-cointro">
	<div class="qaf-container qaf-cointro__grid">
		<div class="qaf-cointro__head">
			<p class="qaf-kicker"<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><span class="qaf-kicker__rule" aria-hidden="true"></span><?php echo qaf_text( 'str_about' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
			<?php if ( $qaf_title ) : ?>
				<h2 class="qaf-cointro__title" data-qaf-anim="words"><?php echo qaf_lines( $qaf_title ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
			<?php endif; ?>
		</div>
		<div class="qaf-cointro__body"<?php echo qaf_anim( 'up', 160 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
			<?php echo wp_kses_post( $qaf_content ); ?>
		</div>
	</div>
</section>
