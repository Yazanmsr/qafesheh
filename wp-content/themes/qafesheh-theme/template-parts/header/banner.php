<?php
/**
 * Inner page banner: layered image, star pattern, breadcrumbs, title.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$qaf_img = qaf_banner_image();
?>
<section class="qaf-banner">
	<?php if ( $qaf_img ) : ?>
		<div class="qaf-banner__media" aria-hidden="true">
			<img src="<?php echo esc_url( $qaf_img ); ?>" alt="" fetchpriority="high" decoding="async">
		</div>
	<?php endif; ?>
	<div class="qaf-banner__shade" aria-hidden="true"></div>
	<svg class="qaf-banner__pattern" aria-hidden="true" width="100%" height="100%">
		<defs><pattern id="qaf-banner-star" width="128" height="128" patternUnits="userSpaceOnUse"><path d="<?php echo esc_attr( qaf_star_path() ); ?>" fill="none" stroke="currentColor" stroke-width="1"/></pattern></defs>
		<rect width="100%" height="100%" fill="url(#qaf-banner-star)"/>
	</svg>

	<div class="qaf-container qaf-banner__in">
		<div<?php echo qaf_anim( 'fade' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php qaf_breadcrumbs(); ?></div>
		<h1 class="qaf-banner__title"<?php echo qaf_anim( 'up', 80 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo esc_html( qaf_banner_title() ); ?></h1>
		<div class="qaf-dbl-rule" aria-hidden="true"<?php echo qaf_anim( 'line', 200 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><span></span><span></span></div>
		<?php $qaf_sub = qaf_banner_subtitle(); ?>
		<?php if ( $qaf_sub ) : ?>
			<p class="qaf-banner__sub"<?php echo qaf_anim( 'up', 260 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo esc_html( $qaf_sub ); ?></p>
		<?php endif; ?>
	</div>
</section>
