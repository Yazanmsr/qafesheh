<?php
/**
 * Company quote + contact card.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$qaf_quote_t = qaf_meta( 'quote_t' );
$qaf_quote   = qaf_meta( 'quote' );
$qaf_email   = qaf_meta( 'email' );
$qaf_phone   = qaf_meta( 'phone' );
$qaf_address = qaf_meta_lines( 'address' );
$qaf_social  = array_filter(
	array(
		'instagram' => qaf_meta( 'instagram' ),
		'facebook'  => qaf_meta( 'facebook' ),
		'youtube'   => qaf_meta( 'youtube' ),
	)
);
if ( ! $qaf_quote_t && ! $qaf_email && ! $qaf_phone && ! $qaf_address ) {
	return;
}
?>
<section class="qaf-cocontact">
	<div class="qaf-cocontact__quote">
		<svg class="qaf-cocontact__pattern" aria-hidden="true" width="100%" height="100%">
			<defs><pattern id="qaf-co-q" width="110" height="110" patternUnits="userSpaceOnUse"><path d="M55 14 L68 42 L96 55 L68 68 L55 96 L42 68 L14 55 L42 42 Z" fill="none" stroke="currentColor" stroke-width="1"/></pattern></defs>
			<rect width="100%" height="100%" fill="url(#qaf-co-q)"/>
		</svg>
		<div class="qaf-cocontact__quoteIn">
			<svg class="qaf-cocontact__mark" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.1" aria-hidden="true"<?php echo qaf_anim( 'zoom' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><path d="M7 7h4v4c0 2.5-1.6 4.3-4 5V13H7zM15 7h4v4c0 2.5-1.6 4.3-4 5V13z"/></svg>
			<?php if ( $qaf_quote_t ) : ?>
				<h2 class="qaf-cocontact__title" data-qaf-anim="words"><?php echo esc_html( $qaf_quote_t ); ?></h2>
			<?php endif; ?>
			<?php if ( $qaf_quote ) : ?>
				<p class="qaf-cocontact__text"<?php echo qaf_anim( 'up', 180 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo esc_html( $qaf_quote ); ?></p>
			<?php endif; ?>
		</div>
	</div>

	<div class="qaf-cocontact__card" data-qaf-stagger>
		<p class="qaf-cocontact__label"<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo qaf_text_f( 'str_contact_co', get_the_title() ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
		<?php if ( $qaf_email ) : ?>
			<a class="qaf-cocontact__row" href="mailto:<?php echo esc_attr( antispambot( $qaf_email ) ); ?>" data-qaf-nofx<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo qaf_icon( 'mail', 17 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( antispambot( $qaf_email ) ); ?></span></a>
		<?php endif; ?>
		<?php if ( $qaf_phone ) : ?>
			<a class="qaf-cocontact__row" href="<?php echo esc_attr( qaf_tel( $qaf_phone ) ); ?>" data-qaf-nofx<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo qaf_icon( 'phone', 17 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span dir="ltr"><?php echo esc_html( $qaf_phone ); ?></span></a>
		<?php endif; ?>
		<?php if ( $qaf_address ) : ?>
			<p class="qaf-cocontact__row qaf-cocontact__row--top"<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo qaf_icon( 'pin', 17 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo implode( '<br>', array_map( 'esc_html', $qaf_address ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span></p>
		<?php endif; ?>
		<?php if ( $qaf_social ) : ?>
			<div class="qaf-social qaf-cocontact__social"<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
				<?php foreach ( $qaf_social as $qaf_net => $qaf_url ) : ?>
					<a href="<?php echo esc_url( $qaf_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( ucfirst( $qaf_net ) ); ?>" data-qaf-nofx><?php echo qaf_icon( $qaf_net, 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
