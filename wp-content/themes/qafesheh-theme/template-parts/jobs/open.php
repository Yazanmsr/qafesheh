<?php
/**
 * Open application band.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$qaf_email = qaf_opt( 'apply_email' );
?>
<section class="qaf-sec qaf-sec--ink qaf-openapp" id="open-application">
	<div class="qaf-container qaf-openapp__grid">
		<div class="qaf-openapp__copy">
			<p class="qaf-kicker"<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><span class="qaf-kicker__rule" aria-hidden="true"></span><?php echo qaf_text( 'open_label' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
			<h2 class="qaf-openapp__title qaf-foil" data-qaf-anim="blur"><?php echo qaf_text( 'open_title' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
			<p class="qaf-openapp__text"<?php echo qaf_anim( 'up', 160 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo qaf_text( 'open_text' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
			<?php if ( $qaf_email && qaf_opt( 'apply_show_email' ) ) : ?>
				<a class="qaf-openapp__mail" href="mailto:<?php echo esc_attr( antispambot( $qaf_email ) ); ?>" data-qaf-nofx<?php echo qaf_anim( 'up', 220 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo qaf_icon( 'mail', 17 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( antispambot( $qaf_email ) ); ?></span></a>
			<?php endif; ?>
		</div>
		<div<?php echo qaf_anim( 'up', 120 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
			<?php get_template_part( 'template-parts/jobs/form', null, array( 'job_id' => 0, 'variant' => 'dark' ) ); ?>
		</div>
	</div>
</section>
