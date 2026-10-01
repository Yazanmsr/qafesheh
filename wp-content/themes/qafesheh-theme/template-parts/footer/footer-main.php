<?php
/**
 * Footer: stitch strip, five columns, legal bar.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$qaf_cols = array(
	array( 'footer_t1', 'footer_group' ),
	array( 'footer_t2', 'footer_brands' ),
	array( 'footer_t3', 'footer_service' ),
);
?>
<footer class="qaf-footer">
	<?php if ( qaf_opt( 'footer_stitch' ) ) : ?>
		<div class="qaf-stitch" aria-hidden="true"></div>
	<?php endif; ?>

	<div class="qaf-container">
		<div class="qaf-footer__grid" data-qaf-stagger>
			<div class="qaf-footer__brand"<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
				<?php qaf_logo( 'footer' ); ?>
				<?php if ( qaf_opt_i18n( 'footer_about' ) ) : ?>
					<p class="qaf-footer__about"><?php echo qaf_text( 'footer_about' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
				<?php endif; ?>
			</div>

			<?php foreach ( $qaf_cols as $qaf_col ) : ?>
				<?php $qaf_menu = qaf_menu( $qaf_col[1], 'qaf-footer__menu' ); ?>
				<div class="qaf-footer__col"<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
					<h2 class="qaf-footer__title"><?php echo qaf_text( $qaf_col[0] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
					<?php echo $qaf_menu; // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
			<?php endforeach; ?>

			<div class="qaf-footer__col qaf-footer__contact"<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
				<h2 class="qaf-footer__title"><?php echo qaf_text( 'footer_t4' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
				<ul class="qaf-footer__menu">
					<?php if ( qaf_opt( 'c_email' ) ) : ?>
						<li><a href="mailto:<?php echo esc_attr( antispambot( qaf_opt( 'c_email' ) ) ); ?>" data-qaf-nofx><?php echo esc_html( antispambot( qaf_opt( 'c_email' ) ) ); ?></a></li>
					<?php endif; ?>
					<?php if ( qaf_opt( 'c_phone' ) ) : ?>
						<li><a href="<?php echo esc_attr( qaf_tel( qaf_opt( 'c_phone' ) ) ); ?>" dir="ltr" data-qaf-nofx><?php echo esc_html( qaf_opt( 'c_phone' ) ); ?></a></li>
					<?php endif; ?>
					<?php if ( qaf_opt_i18n( 'c_address' ) ) : ?>
						<li><span><?php echo qaf_text( 'c_address' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span></li>
					<?php endif; ?>
				</ul>
				<?php qaf_social( 'qaf-social qaf-footer__social' ); ?>
			</div>
		</div>

		<div class="qaf-footer__bar">
			<span class="qaf-footer__copy"><?php echo esc_html( str_replace( '{year}', wp_date( 'Y' ), (string) qaf_opt_i18n( 'footer_copy' ) ) ); ?></span>
			<div class="qaf-footer__legal">
				<?php echo qaf_menu( 'footer_legal', 'qaf-footer__legal-menu' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php if ( qaf_opt( 'footer_credit' ) ) : ?>
					<a class="qaf-footer__credit" href="https://qb.studio" target="_blank" rel="noopener" data-qaf-nofx><?php echo qaf_text( 'footer_credit_text' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</footer>
