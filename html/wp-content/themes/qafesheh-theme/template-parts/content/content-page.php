<?php
/**
 * Classic page body.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;
?>
<article <?php post_class( 'qaf-sec qaf-single' ); ?>>
	<div class="qaf-container qaf-single__in">
		<div class="qaf-prose"<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
			<?php
			the_content();
			wp_link_pages();
			?>
		</div>
	</div>
</article>
