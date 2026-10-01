<?php
/**
 * Document head, loader, header and main wrapper.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php if ( qaf_loader_enabled() ) : ?>
	<?php get_template_part( 'template-parts/header/loader' ); ?>
<?php endif; ?>

<a class="qaf-skip" href="#qaf-main"><?php echo qaf_text( 'str_skip' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>

<?php if ( qaf_opt( 'progress' ) ) : ?>
	<div class="qaf-progress" aria-hidden="true"><span></span></div>
<?php endif; ?>

<div class="qaf-page">
<?php
if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) {
	get_template_part( 'template-parts/header/header' );
}
?>
<main id="qaf-main" class="qaf-main" tabindex="-1">
<?php
if ( qaf_has_banner() ) {
	get_template_part( 'template-parts/header/banner' );
}
