<?php
/**
 * Careers hero: copy | image.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="qaf-jhero" data-qaf-hero>
	<div class="qaf-jhero__copy">
		<svg class="qaf-phero__pattern" aria-hidden="true" width="100%" height="100%">
			<defs><pattern id="qaf-jobs-star" width="128" height="128" patternUnits="userSpaceOnUse"><path d="<?php echo esc_attr( qaf_star_path() ); ?>" fill="none" stroke="currentColor" stroke-width="1"/></pattern></defs>
			<rect width="100%" height="100%" fill="url(#qaf-jobs-star)"/>
		</svg>
		<div class="qaf-jhero__in">
			<div<?php echo qaf_anim( 'fade' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php qaf_breadcrumbs(); ?></div>
			<h1 class="qaf-jhero__title qaf-foil" data-qaf-anim="blur" style="--qaf-delay:120ms"><?php echo qaf_text( 'jobs_title' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h1>
			<p class="qaf-jhero__text"<?php echo qaf_anim( 'up', 280 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo qaf_text( 'jobs_text' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
		</div>
	</div>
	<?php if ( qaf_opt( 'jobs_image' ) ) : ?>
		<div class="qaf-jhero__media qaf-hero__media" aria-hidden="true">
			<?php echo qaf_image( qaf_opt( 'jobs_image' ), 'qaf-wide', array( 'class' => 'qaf-hero__img', 'loading' => 'eager', 'fetchpriority' => 'high', 'sizes' => '(max-width: 900px) 100vw, 47vw', 'style' => 'object-position:left center' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
	<?php endif; ?>
</section>
