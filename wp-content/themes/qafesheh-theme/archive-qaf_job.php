<?php
/**
 * Careers: split hero, why us, filters + vacancies, open application.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

get_header();
get_template_part( 'template-parts/jobs/hero' );
get_template_part( 'template-parts/jobs/why' );
?>
<section class="qaf-sec qaf-jobs" data-qaf-jobs>
	<div class="qaf-container qaf-jobs__grid">
		<?php get_template_part( 'template-parts/jobs/filters' ); ?>
		<div class="qaf-jobs__main">
			<div class="qaf-jobs__head">
				<h2 class="qaf-jobs__title" data-qaf-anim="words"><?php echo qaf_text( 'str_vacancies' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
				<p class="qaf-jobs__count" aria-live="polite"><span data-qaf-jobs-count><?php echo esc_html( (string) $GLOBALS['wp_query']->post_count ); ?></span> <?php echo esc_html( trim( str_replace( '%s', '', (string) qaf_opt_i18n( 'str_jobs_count' ) ) ) ); ?></p>
			</div>
			<?php if ( have_posts() ) : ?>
				<ul class="qaf-jobs__list" data-qaf-stagger>
					<?php
					while ( have_posts() ) {
						the_post();
						get_template_part( 'template-parts/jobs/card' );
					}
					?>
				</ul>
				<p class="qaf-jobs__empty" hidden><?php echo qaf_text( 'str_no_results' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
			<?php else : ?>
				<p class="qaf-jobs__empty"><?php echo qaf_text( 'str_no_results' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php
get_template_part( 'template-parts/jobs/open' );
get_footer();
