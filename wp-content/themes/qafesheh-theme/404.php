<?php
/**
 * 404.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="qaf-404">
	<div class="qaf-lines" aria-hidden="true"></div>
	<div class="qaf-container qaf-404__in">
		<span class="qaf-404__code" dir="ltr"<?php echo qaf_anim( 'fade' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>404</span>
		<h1 class="qaf-404__title"<?php echo qaf_anim( 'up', 80 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo qaf_text( 'str_404' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h1>
		<div class="qaf-dbl-rule" aria-hidden="true"<?php echo qaf_anim( 'line', 160 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><span></span><span></span></div>
		<p class="qaf-404__text"<?php echo qaf_anim( 'up', 220 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo qaf_text( 'str_404_text' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
		<div<?php echo qaf_anim( 'up', 300 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php qaf_button( qaf_opt_i18n( 'str_back_home' ), apply_filters( 'wpml_home_url', home_url( '/' ) ), 'solid', 'qaf-btn--lg' ); ?></div>
	</div>
</section>
<?php
get_footer();
