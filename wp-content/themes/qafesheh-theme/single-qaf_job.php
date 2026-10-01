<?php
/**
 * Job page: header with tags + apply, body (about, tasks, requirements, form) + sidebar.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) {
	the_post();
	$qaf_closed = (bool) qaf_meta( 'closed' );
	$qaf_terms  = array();
	foreach ( array( 'qaf_job_dept', 'qaf_job_loc', 'qaf_job_type' ) as $qaf_tax ) {
		$qaf_t = get_the_terms( get_the_ID(), $qaf_tax );
		if ( $qaf_t && ! is_wp_error( $qaf_t ) ) {
			$qaf_terms[ $qaf_tax ] = $qaf_t[0]->name;
		}
	}
	?>
	<section class="qaf-phero qaf-jobhead">
		<svg class="qaf-phero__pattern" aria-hidden="true" width="100%" height="100%">
			<defs><pattern id="qaf-job-star" width="128" height="128" patternUnits="userSpaceOnUse"><path d="<?php echo esc_attr( qaf_star_path() ); ?>" fill="none" stroke="currentColor" stroke-width="1"/></pattern></defs>
			<rect width="100%" height="100%" fill="url(#qaf-job-star)"/>
		</svg>
		<div class="qaf-container qaf-phero__in">
			<div class="qaf-phero__main">
				<div<?php echo qaf_anim( 'fade' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php qaf_breadcrumbs(); ?></div>
				<h1 class="qaf-phero__title qaf-foil" data-qaf-anim="blur" style="--qaf-delay:100ms"><?php the_title(); ?></h1>
				<?php if ( $qaf_terms ) : ?>
					<div class="qaf-jobhead__tags" data-qaf-stagger>
						<?php foreach ( $qaf_terms as $qaf_name ) : ?>
							<span class="qaf-jobhead__tag"<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo esc_html( $qaf_name ); ?></span>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
			<?php if ( ! $qaf_closed ) : ?>
				<div<?php echo qaf_anim( 'up', 260 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php qaf_button( qaf_opt_i18n( 'str_apply' ), '#apply', 'solid', 'qaf-btn--lg' ); ?></div>
			<?php endif; ?>
		</div>
	</section>

	<section class="qaf-sec qaf-jobbody">
		<div class="qaf-container qaf-jobbody__grid">
			<div class="qaf-jobbody__main">
				<?php get_template_part( 'template-parts/jobs/body' ); ?>
			</div>
			<aside class="qaf-jobbody__side">
				<?php get_template_part( 'template-parts/jobs/sidebar', null, array( 'terms' => $qaf_terms ) ); ?>
			</aside>
		</div>
	</section>
	<?php
}

get_footer();
