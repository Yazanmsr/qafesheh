<?php
/**
 * Desktop navigation.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$qaf_nav = qaf_menu( 'primary', 'qaf-nav__list', 2 );
if ( ! $qaf_nav ) {
	return;
}
?>
<nav class="qaf-nav" aria-label="<?php echo esc_attr( qaf_opt_i18n( 'str_menu' ) ); ?>">
	<?php echo $qaf_nav; // phpcs:ignore WordPress.Security.EscapeOutput ?>
	<span class="qaf-nav__ink" aria-hidden="true"></span>
</nav>
