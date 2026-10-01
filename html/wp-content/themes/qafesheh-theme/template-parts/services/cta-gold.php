<?php
/**
 * Gold CTA band under the services list.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$qaf_title = qaf_opt_i18n( 'svc_cta_title' );
if ( ! $qaf_title ) {
	return;
}
?>
<section class="qaf-goldcta">
	<svg class="qaf-goldcta__pattern" aria-hidden="true" width="100%" height="100%">
		<defs><pattern id="qaf-gold-star" width="96" height="96" patternUnits="userSpaceOnUse"><path d="M48 12 L59 37 L84 48 L59 59 L48 84 L37 59 L12 48 L37 37 Z" fill="none" stroke="currentColor" stroke-width="1"/></pattern></defs>
		<rect width="100%" height="100%" fill="url(#qaf-gold-star)"/>
	</svg>
	<div class="qaf-container qaf-goldcta__in">
		<div class="qaf-goldcta__copy">
			<h2 class="qaf-goldcta__title" data-qaf-anim="words"><?php echo esc_html( $qaf_title ); ?></h2>
			<?php if ( qaf_opt_i18n( 'svc_cta_text' ) ) : ?>
				<p class="qaf-goldcta__text"<?php echo qaf_anim( 'up', 140 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo qaf_text( 'svc_cta_text' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
			<?php endif; ?>
		</div>
		<div<?php echo qaf_anim( 'up', 220 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php qaf_button( qaf_opt_i18n( 'svc_cta_btn' ), qaf_opt_i18n( 'svc_cta_url' ), 'ink', 'qaf-btn--lg' ); ?></div>
	</div>
</section>
