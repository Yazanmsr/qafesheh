<?php
/**
 * Job sidebar: details + other vacancies.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$qaf_terms = $args['terms'] ?? array();
$qaf_rows  = array_filter(
	array(
		'str_dept'       => $qaf_terms['qaf_job_dept'] ?? '',
		'str_location'   => $qaf_terms['qaf_job_loc'] ?? '',
		'str_jobtype'    => $qaf_terms['qaf_job_type'] ?? '',
		'str_experience' => qaf_meta( 'experience' ),
		'str_deadline'   => qaf_meta( 'deadline' ),
	)
);
$qaf_others = get_posts( array( 'post_type' => 'qaf_job', 'posts_per_page' => 4, 'post__not_in' => array( get_the_ID() ), 'suppress_filters' => false ) );
?>
<?php if ( $qaf_rows ) : ?>
	<div class="qaf-side qaf-side--soft"<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
		<p class="qaf-side__label"><?php echo qaf_text( 'str_job_details' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
		<dl class="qaf-jdetails">
			<?php foreach ( $qaf_rows as $qaf_key => $qaf_val ) : ?>
				<div class="qaf-jdetails__row"><dt><?php echo qaf_text( $qaf_key ); // phpcs:ignore WordPress.Security.EscapeOutput ?></dt><dd><?php echo esc_html( $qaf_val ); ?></dd></div>
			<?php endforeach; ?>
		</dl>
	</div>
<?php endif; ?>

<?php if ( $qaf_others ) : ?>
	<div class="qaf-side qaf-side--ink"<?php echo qaf_anim( 'up', 100 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
		<p class="qaf-side__label"><?php echo qaf_text( 'str_other_jobs' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
		<ul class="qaf-jothers">
			<?php foreach ( $qaf_others as $qaf_p ) : ?>
				<?php $qaf_loc = get_the_terms( $qaf_p->ID, 'qaf_job_loc' ); ?>
				<li>
					<a href="<?php echo esc_url( get_permalink( $qaf_p ) ); ?>">
						<span class="qaf-jothers__title"><?php echo esc_html( get_the_title( $qaf_p ) ); ?></span>
						<?php if ( $qaf_loc && ! is_wp_error( $qaf_loc ) ) : ?>
							<span class="qaf-jothers__loc"><?php echo esc_html( $qaf_loc[0]->name ); ?></span>
						<?php endif; ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>
