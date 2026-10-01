<?php
/**
 * Comments (disabled by design on this site; kept minimal for completeness).
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

if ( post_password_required() || ( ! comments_open() && ! get_comments_number() ) ) {
	return;
}
?>
<div class="qaf-comments">
	<?php if ( have_comments() ) : ?>
		<ol class="qaf-comments__list"><?php wp_list_comments( array( 'style' => 'ol', 'short_ping' => true ) ); ?></ol>
	<?php endif; ?>
	<?php comment_form(); ?>
</div>
