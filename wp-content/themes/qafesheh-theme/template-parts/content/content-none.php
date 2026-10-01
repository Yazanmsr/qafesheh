<?php
/**
 * Empty state.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="qaf-empty"<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<p><?php echo qaf_text( 'str_no_results' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
	<?php get_search_form(); ?>
</div>
