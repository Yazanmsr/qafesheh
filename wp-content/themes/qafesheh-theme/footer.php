<?php
/**
 * Closes main, prints footer, mobile menu and page chrome.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;
?>
</main>
<?php
if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) {
	get_template_part( 'template-parts/footer/footer-main' );
}
?>
</div>

<?php get_template_part( 'template-parts/header/mobile-menu' ); ?>

<?php if ( qaf_opt( 'scroll_top' ) ) : ?>
	<button class="qaf-top" type="button" aria-label="<?php echo esc_attr( qaf_opt_i18n( 'str_back_top' ) ); ?>">
		<?php echo qaf_icon( 'arrow-up', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	</button>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
