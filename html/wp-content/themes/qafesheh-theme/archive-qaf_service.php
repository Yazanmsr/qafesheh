<?php
/**
 * Services archive: hero, editorial service rows, gold CTA band.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

get_header();

get_template_part(
	'template-parts/header/page-hero',
	null,
	array(
		'title' => qaf_opt_i18n( 'svc_title' ),
		'text'  => qaf_opt_i18n( 'svc_text' ),
	)
);
?>
<section class="qaf-sec qaf-svclist">
	<div class="qaf-container">
		<?php if ( have_posts() ) : ?>
			<ol class="qaf-svclist__rows">
				<?php
				$qaf_i = 0;
				while ( have_posts() ) {
					the_post();
					get_template_part( 'template-parts/services/row', null, array( 'index' => ++$qaf_i ) );
				}
				?>
			</ol>
			<?php qaf_pagination(); ?>
		<?php else : ?>
			<?php get_template_part( 'template-parts/content/content', 'none' ); ?>
		<?php endif; ?>
	</div>
</section>
<?php
get_template_part( 'template-parts/services/cta-gold' );
get_footer();
