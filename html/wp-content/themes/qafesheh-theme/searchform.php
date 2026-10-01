<?php
/**
 * Search form (carries the language for WPML).
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;
?>
<form role="search" method="get" class="qaf-search" action="<?php echo esc_url( apply_filters( 'wpml_home_url', home_url( '/' ) ) ); ?>">
	<label class="screen-reader-text" for="qaf-s"><?php echo qaf_text( 'str_search' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></label>
	<input id="qaf-s" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php echo esc_attr( qaf_opt_i18n( 'str_search' ) ); ?>">
	<?php do_action( 'wpml_add_language_form_field' ); ?>
	<button type="submit" aria-label="<?php echo esc_attr( qaf_opt_i18n( 'str_search' ) ); ?>"><?php echo qaf_icon( 'search', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
</form>
