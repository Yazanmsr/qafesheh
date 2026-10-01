<?php
/**
 * Brand filter chips.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$qaf_terms = qaf_brand_terms();
if ( count( $qaf_terms ) < 2 ) {
	return;
}
?>
<div class="qaf-brfilter">
	<div class="qaf-container qaf-brfilter__in" role="group" aria-label="<?php echo esc_attr( qaf_opt_i18n( 'str_filter' ) ); ?>">
		<span class="qaf-brfilter__label"><?php echo qaf_text( 'str_filter' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		<button type="button" class="qaf-chip is-active" data-brand="" aria-pressed="true"><?php echo qaf_text( 'str_all' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
		<?php foreach ( $qaf_terms as $qaf_term ) : ?>
			<button type="button" class="qaf-chip qaf-tag--<?php echo esc_attr( qaf_brand_base_slug( $qaf_term->slug ) ); ?>" data-brand="<?php echo esc_attr( qaf_brand_base_slug( $qaf_term->slug ) ); ?>" aria-pressed="false"><?php echo esc_html( $qaf_term->name ); ?></button>
		<?php endforeach; ?>
	</div>
</div>
