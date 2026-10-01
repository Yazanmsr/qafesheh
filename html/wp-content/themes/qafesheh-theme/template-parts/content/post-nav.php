<?php
/**
 * Previous / next entry.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$qaf_prev = get_previous_post();
$qaf_next = get_next_post();
if ( ! $qaf_prev && ! $qaf_next ) {
	return;
}
?>
<nav class="qaf-postnav" aria-label="posts">
	<?php if ( $qaf_prev ) : ?>
		<a class="qaf-postnav__item qaf-postnav__item--prev" href="<?php echo esc_url( get_permalink( $qaf_prev ) ); ?>"><?php echo qaf_icon( 'arrow', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( get_the_title( $qaf_prev ) ); ?></span></a>
	<?php endif; ?>
	<?php if ( $qaf_next ) : ?>
		<a class="qaf-postnav__item qaf-postnav__item--next" href="<?php echo esc_url( get_permalink( $qaf_next ) ); ?>"><span><?php echo esc_html( get_the_title( $qaf_next ) ); ?></span><?php echo qaf_icon( 'arrow', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
	<?php endif; ?>
</nav>
