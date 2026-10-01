<?php
/**
 * Vacancy filters: department / location / type (client-side, counts from published jobs).
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$qaf_groups = array(
	'qaf_job_dept' => array( 'dept', 'str_dept' ),
	'qaf_job_loc'  => array( 'loc', 'str_location' ),
	'qaf_job_type' => array( 'type', 'str_jobtype' ),
);
$qaf_total = (int) $GLOBALS['wp_query']->post_count;
?>
<aside class="qaf-jfilters" aria-label="filters">
	<?php foreach ( $qaf_groups as $qaf_tax => $qaf_g ) : ?>
		<?php
		$qaf_terms = get_terms( array( 'taxonomy' => $qaf_tax, 'hide_empty' => true ) );
		if ( is_wp_error( $qaf_terms ) || ! $qaf_terms ) {
			continue;
		}
		?>
		<div class="qaf-jfilters__group" data-group="<?php echo esc_attr( $qaf_g[0] ); ?>"<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
			<p class="qaf-side__label"><?php echo qaf_text( $qaf_g[1] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
			<ul class="qaf-jfilters__list">
				<?php if ( 'dept' === $qaf_g[0] ) : ?>
					<li><button type="button" class="qaf-jfilter is-active" data-value="" aria-pressed="true"><span><?php echo qaf_text( 'str_all' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span><em><?php echo esc_html( (string) $qaf_total ); ?></em></button></li>
				<?php endif; ?>
				<?php foreach ( $qaf_terms as $qaf_term ) : ?>
					<li><button type="button" class="qaf-jfilter" data-value="<?php echo esc_attr( $qaf_term->slug ); ?>" aria-pressed="false"><span><?php echo esc_html( $qaf_term->name ); ?></span><em><?php echo esc_html( (string) $qaf_term->count ); ?></em></button></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endforeach; ?>

	<a class="qaf-jfilters__open" href="#open-application"<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
		<span class="qaf-jfilters__openTitle"><?php echo qaf_text( 'side_title' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		<span class="qaf-jfilters__openText"><?php echo qaf_text( 'side_text' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		<?php echo qaf_icon( 'arrow', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	</a>
</aside>
