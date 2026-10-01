<?php
/**
 * Designed page hero for section pages.
 *
 * variant split: dark band, breadcrumbs + gold title on one side, intro text on the other.
 * variant media: full image with layered shade, optional icon/logo, title, text and actions.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$a = wp_parse_args(
	$args ?? array(),
	array(
		'variant'  => 'split',
		'title'    => '',
		'text'     => '',
		'image'    => '',
		'position' => 'center',
		'icon'     => '',
		'class'    => '',
		'align'    => 'start',
		'ghost'    => '',
		'tag'      => 'h1',
	)
);
$qaf_media = 'media' === $a['variant'] && $a['image'];
$qaf_stack = $qaf_media || 'stack' === $a['variant'];
?>
<section class="qaf-phero qaf-phero--<?php echo esc_attr( $qaf_media ? 'media' : ( $qaf_stack ? 'stack' : 'split' ) ); ?><?php echo 'center' === $a['align'] ? ' qaf-phero--center' : ''; ?> <?php echo esc_attr( $a['class'] ); ?>"<?php echo $qaf_media ? ' data-qaf-hero' : ''; ?>>
	<?php if ( $qaf_media ) : ?>
		<div class="qaf-phero__media qaf-hero__media" aria-hidden="true">
			<?php echo qaf_image( $a['image'], 'qaf-banner', array( 'class' => 'qaf-hero__img', 'loading' => 'eager', 'fetchpriority' => 'high', 'sizes' => '100vw', 'style' => 'object-position:' . preg_replace( '/[^a-z0-9% .]/i', '', (string) $a['position'] ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
		<div class="qaf-phero__shade" aria-hidden="true"></div>
	<?php endif; ?>
	<svg class="qaf-phero__pattern" aria-hidden="true" width="100%" height="100%">
		<defs><pattern id="qaf-phero-star" width="128" height="128" patternUnits="userSpaceOnUse"><path d="<?php echo esc_attr( qaf_star_path() ); ?>" fill="none" stroke="currentColor" stroke-width="1"/></pattern></defs>
		<rect width="100%" height="100%" fill="url(#qaf-phero-star)"/>
	</svg>

	<?php if ( $a['ghost'] ) : ?>
		<span class="qaf-phero__ghost" aria-hidden="true"><?php echo esc_html( $a['ghost'] ); ?></span>
	<?php endif; ?>
	<div class="qaf-container qaf-phero__in">
		<div class="qaf-phero__main">
			<div<?php echo qaf_anim( 'fade' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php qaf_breadcrumbs(); ?></div>
			<div class="qaf-phero__titleRow">
				<?php if ( $a['icon'] ) : ?>
					<span class="qaf-phero__icon"<?php echo qaf_anim( 'zoom', 80 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo qaf_icon( $a['icon'], 46 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<?php endif; ?>
				<?php $qaf_h = 'h2' === $a['tag'] ? 'h2' : 'h1'; ?>
				<<?php echo $qaf_h; // phpcs:ignore WordPress.Security.EscapeOutput ?> class="qaf-phero__title qaf-foil" data-qaf-anim="blur" style="--qaf-delay:80ms"><?php echo qaf_lines( $a['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></<?php echo $qaf_h; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
			</div>
			<?php if ( $qaf_stack && $a['text'] ) : ?>
				<p class="qaf-phero__text"<?php echo qaf_anim( 'up', 320 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo esc_html( $a['text'] ); ?></p>
			<?php endif; ?>
		</div>
		<?php if ( ! $qaf_stack && $a['text'] ) : ?>
			<p class="qaf-phero__text"<?php echo qaf_anim( 'up', 260 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo esc_html( $a['text'] ); ?></p>
		<?php endif; ?>
	</div>
</section>
