<?php
/**
 * Why work with us (four option slots).
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$qaf_items = array();
for ( $qaf_n = 1; $qaf_n <= 4; $qaf_n++ ) {
	$qaf_title = (string) qaf_opt_i18n( 'why' . $qaf_n . '_title' );
	if ( '' !== $qaf_title ) {
		$qaf_items[] = array( qaf_opt( 'why' . $qaf_n . '_icon' ), $qaf_title, (string) qaf_opt_i18n( 'why' . $qaf_n . '_text' ) );
	}
}
if ( ! $qaf_items ) {
	return;
}
?>
<section class="qaf-why">
	<div class="qaf-why__grid" style="--qaf-cols:<?php echo absint( count( $qaf_items ) ); ?>" data-qaf-stagger>
		<?php foreach ( $qaf_items as $qaf_item ) : ?>
			<div class="qaf-why__item"<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
				<?php echo qaf_icon( $qaf_item[0], 26, 'qaf-why__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<h2 class="qaf-why__title"><?php echo esc_html( $qaf_item[1] ); ?></h2>
				<?php if ( $qaf_item[2] ) : ?>
					<p class="qaf-why__text"><?php echo esc_html( $qaf_item[2] ); ?></p>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
</section>
