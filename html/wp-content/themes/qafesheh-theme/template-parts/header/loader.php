<?php
/**
 * Loader — full intro (first visit) or quick curtain (next pages), decided in <head> before paint.
 * Two curtains (ink + gold) lift in sequence, so the exit reads as one soft golden sweep.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$qaf_mark = qaf_opt( 'loader_mark' );
$qaf_logo = qaf_opt_url( 'logo' );
?>
<div class="qaf-loader" id="qaf-loader" role="status" aria-live="polite" aria-label="<?php echo esc_attr( qaf_opt_i18n( 'brand_name' ) ); ?>">
	<div class="qaf-loader__gold" aria-hidden="true"></div>
	<div class="qaf-loader__ink">
		<svg class="qaf-loader__pattern" aria-hidden="true" width="100%" height="100%">
			<defs>
				<pattern id="qaf-loader-star" width="128" height="128" patternUnits="userSpaceOnUse">
					<path d="<?php echo esc_attr( qaf_star_path() ); ?>" fill="none" stroke="currentColor" stroke-width="1"/>
				</pattern>
			</defs>
			<rect width="100%" height="100%" fill="url(#qaf-loader-star)"/>
		</svg>

		<div class="qaf-loader__stage">
			<div class="qaf-loader__mark" aria-hidden="true">
				<?php if ( 'logo' === $qaf_mark && $qaf_logo ) : ?>
					<img src="<?php echo esc_url( $qaf_logo ); ?>" alt="" width="120" height="120">
				<?php else : ?>
					<svg viewBox="0 0 128 128" width="120" height="120">
						<rect class="qaf-loader__sq qaf-loader__sq--a" x="30" y="30" width="68" height="68" pathLength="1"/>
						<rect class="qaf-loader__sq qaf-loader__sq--b" x="30" y="30" width="68" height="68" pathLength="1" transform="rotate(45 64 64)"/>
						<path class="qaf-loader__star" d="<?php echo esc_attr( qaf_star_path() ); ?>" pathLength="1"/>
						<circle class="qaf-loader__dot" cx="64" cy="64" r="3"/>
					</svg>
				<?php endif; ?>
			</div>

			<div class="qaf-loader__name"><?php echo esc_html( qaf_opt_i18n( 'brand_name' ) ); ?></div>
			<div class="qaf-loader__sub" dir="ltr"><?php echo esc_html( qaf_opt_i18n( 'brand_sub' ) ); ?></div>

			<div class="qaf-loader__meter" aria-hidden="true">
				<span class="qaf-loader__bar"><span></span></span>
				<span class="qaf-loader__count" dir="ltr"><b>0</b><i>%</i></span>
			</div>
		</div>
	</div>
</div>
