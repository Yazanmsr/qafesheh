<?php
/**
 * Service body.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$qaf_includes = qaf_meta_lines( 'includes' );
$qaf_steps    = qaf_meta_lines( 'steps' );
$qaf_gallery  = array_slice( qaf_meta_gallery(), 0, 2 );
?>
<div class="qaf-svcsingle__intro qaf-prose"<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<?php the_content(); ?>
</div>

<?php if ( $qaf_includes ) : ?>
	<div class="qaf-svcsingle__block">
		<h2 class="qaf-svcsingle__h" data-qaf-anim="words"><?php echo qaf_text( 'str_includes' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
		<ul class="qaf-checks" data-qaf-stagger>
			<?php foreach ( $qaf_includes as $qaf_line ) : ?>
				<li<?php echo qaf_anim( 'start' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><svg class="qaf-checks__mark" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true"><path d="M12 2l3 7 7 3-7 3-3 7-3-7-7-3 7-3z"/></svg><span><?php echo esc_html( $qaf_line ); ?></span></li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>

<?php if ( $qaf_gallery ) : ?>
	<div class="qaf-svcsingle__pics" data-qaf-stagger>
		<?php foreach ( $qaf_gallery as $qaf_img ) : ?>
			<figure class="qaf-svcsingle__pic"<?php echo qaf_anim( 'reveal' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><span><?php echo wp_get_attachment_image( $qaf_img, 'qaf-thumb', false, array( 'sizes' => '(max-width: 700px) 100vw, 30vw' ) ); ?></span></figure>
		<?php endforeach; ?>
	</div>
<?php endif; ?>

<?php if ( $qaf_steps ) : ?>
	<div class="qaf-svcsingle__block">
		<h2 class="qaf-svcsingle__h" data-qaf-anim="words"><?php echo qaf_text( 'str_steps' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
		<ol class="qaf-steps" style="--qaf-cols:<?php echo absint( min( 5, count( $qaf_steps ) ) ); ?>" data-qaf-steps>
			<?php foreach ( $qaf_steps as $qaf_i => $qaf_step ) : ?>
				<li class="qaf-steps__item<?php echo 0 === $qaf_i ? ' is-active' : ''; ?>">
					<span class="qaf-steps__num"><?php echo esc_html( str_pad( (string) ( $qaf_i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
					<span class="qaf-steps__name"><?php echo esc_html( $qaf_step ); ?></span>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
<?php endif; ?>
