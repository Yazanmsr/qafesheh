<?php
/**
 * Mobile menu — full-screen panel with numbered links.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$qaf_nav = qaf_menu( 'primary', 'qaf-mobile__list', 2 );
?>
<div class="qaf-mobile" id="qaf-mobile" aria-hidden="true" inert>
	<div class="qaf-mobile__bg" aria-hidden="true"></div>
	<div class="qaf-mobile__panel" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr( qaf_opt_i18n( 'str_menu' ) ); ?>">
		<div class="qaf-mobile__head">
			<?php qaf_logo( 'mobile' ); ?>
			<button class="qaf-mobile__close" type="button" aria-label="<?php echo esc_attr( qaf_opt_i18n( 'str_close' ) ); ?>">
				<?php echo qaf_icon( 'close', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</button>
		</div>

		<nav class="qaf-mobile__nav">
			<?php echo $qaf_nav; // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</nav>

		<div class="qaf-mobile__foot">
			<?php qaf_button( qaf_opt_i18n( 'header_cta_text' ), qaf_opt_i18n( 'header_cta_url' ), 'solid', 'qaf-mobile__cta' ); ?>
			<div class="qaf-mobile__meta">
				<?php if ( qaf_opt( 'c_phone' ) ) : ?>
					<a href="<?php echo esc_attr( qaf_tel( qaf_opt( 'c_phone' ) ) ); ?>" dir="ltr" data-qaf-nofx><?php echo esc_html( qaf_opt( 'c_phone' ) ); ?></a>
				<?php endif; ?>
				<?php qaf_lang_switcher( 'qaf-lang qaf-mobile__lang' ); ?>
			</div>
			<?php qaf_social( 'qaf-social qaf-mobile__social' ); ?>
		</div>
	</div>
</div>
