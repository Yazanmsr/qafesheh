<?php
/**
 * Branch card.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$qaf_b = $args['post'] ?? null;
if ( ! $qaf_b instanceof WP_Post ) {
	return;
}
$qaf_id     = $qaf_b->ID;
$qaf_terms  = get_the_terms( $qaf_id, 'qaf_brand' );
$qaf_brands = $qaf_terms && ! is_wp_error( $qaf_terms ) ? array_map( static fn( $t ) => qaf_brand_base_slug( $t->slug ), $qaf_terms ) : array();
$qaf_phone  = qaf_meta( 'phone', $qaf_id );
?>
<li class="qaf-brcard<?php echo ! empty( $args['active'] ) ? ' is-active' : ''; ?>" id="branch-<?php echo esc_attr( $qaf_b->post_name ); ?>" data-brands="<?php echo esc_attr( implode( ' ', $qaf_brands ) ); ?>" data-map="<?php echo esc_url( qaf_branch_embed( $qaf_id ) ); ?>" data-name="<?php echo esc_attr( get_the_title( $qaf_b ) ); ?>" data-link="<?php echo esc_url( qaf_meta( 'map_url', $qaf_id ) ); ?>"<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="qaf-brcard__top">
		<div class="qaf-brcard__names">
			<h2 class="qaf-brcard__title"><?php echo esc_html( get_the_title( $qaf_b ) ); ?></h2>
			<?php if ( qaf_meta( 'address', $qaf_id ) ) : ?>
				<p class="qaf-brcard__addr"><?php echo esc_html( qaf_meta( 'address', $qaf_id ) ); ?></p>
			<?php endif; ?>
		</div>
		<?php qaf_brand_tags( $qaf_id, 'qaf-tags qaf-brcard__tags' ); ?>
	</div>
	<div class="qaf-brcard__meta">
		<?php if ( qaf_meta( 'hours', $qaf_id ) ) : ?>
			<span class="qaf-brcard__item"><?php echo qaf_icon( 'clock', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( qaf_meta( 'hours', $qaf_id ) ); ?></span></span>
		<?php endif; ?>
		<?php if ( $qaf_phone ) : ?>
			<a class="qaf-brcard__item" href="<?php echo esc_attr( qaf_tel( $qaf_phone ) ); ?>" data-qaf-nofx><?php echo qaf_icon( 'phone', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span dir="ltr"><?php echo esc_html( $qaf_phone ); ?></span></a>
		<?php endif; ?>
		<button type="button" class="qaf-brcard__map" aria-pressed="<?php echo ! empty( $args['active'] ) ? 'true' : 'false'; ?>"><?php echo qaf_icon( 'pin', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo qaf_text( 'str_on_map' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span></button>
	</div>
</li>
