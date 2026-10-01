<?php
/**
 * Job body.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$qaf_tasks = qaf_meta_lines( 'tasks' );
$qaf_reqs  = qaf_meta_lines( 'reqs' );
?>
<?php if ( trim( wp_strip_all_tags( get_the_content() ) ) ) : ?>
	<div class="qaf-svcsingle__block">
		<h2 class="qaf-svcsingle__h" data-qaf-anim="words"><?php echo qaf_text( 'str_about_job' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
		<div class="qaf-prose"<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php the_content(); ?></div>
	</div>
<?php endif; ?>

<?php foreach ( array( 'str_tasks' => $qaf_tasks, 'str_reqs' => $qaf_reqs ) as $qaf_label => $qaf_lines ) : ?>
	<?php if ( $qaf_lines ) : ?>
		<div class="qaf-svcsingle__block">
			<h2 class="qaf-svcsingle__h" data-qaf-anim="words"><?php echo qaf_text( $qaf_label ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
			<ul class="qaf-checks" data-qaf-stagger>
				<?php foreach ( $qaf_lines as $qaf_line ) : ?>
					<li<?php echo qaf_anim( 'start' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><svg class="qaf-checks__mark" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true"><path d="M12 2l3 7 7 3-7 3-3 7-3-7-7-3 7-3z"/></svg><span><?php echo esc_html( $qaf_line ); ?></span></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>
<?php endforeach; ?>

<div class="qaf-jobbody__apply" id="apply"<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<h2 class="qaf-jobbody__formTitle"><?php echo qaf_text( 'str_form_title' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
	<?php if ( qaf_meta( 'closed' ) ) : ?>
		<p class="qaf-jobbody__closed"><?php echo qaf_text( 'str_closed' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
	<?php else : ?>
		<?php get_template_part( 'template-parts/jobs/form', null, array( 'job_id' => get_the_ID(), 'variant' => 'light' ) ); ?>
	<?php endif; ?>
</div>
