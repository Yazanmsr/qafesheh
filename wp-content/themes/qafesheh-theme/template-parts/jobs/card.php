<?php
/**
 * Vacancy row.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$qaf_id    = get_the_ID();
$qaf_attrs = array();
$qaf_meta  = array();
foreach ( array( 'qaf_job_dept' => 'dept', 'qaf_job_loc' => 'loc', 'qaf_job_type' => 'type' ) as $qaf_tax => $qaf_key ) {
	$qaf_terms              = get_the_terms( $qaf_id, $qaf_tax );
	$qaf_terms              = $qaf_terms && ! is_wp_error( $qaf_terms ) ? $qaf_terms : array();
	$qaf_attrs[ $qaf_key ]  = implode( ' ', wp_list_pluck( $qaf_terms, 'slug' ) );
	if ( $qaf_terms ) {
		$qaf_meta[] = $qaf_terms[0]->name;
	}
}
$qaf_closed = (bool) qaf_meta( 'closed' );
?>
<li class="qaf-jcard<?php echo $qaf_closed ? ' is-closed' : ''; ?>" data-dept="<?php echo esc_attr( $qaf_attrs['dept'] ); ?>" data-loc="<?php echo esc_attr( $qaf_attrs['loc'] ); ?>" data-type="<?php echo esc_attr( $qaf_attrs['type'] ); ?>"<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<a class="qaf-jcard__link" href="<?php the_permalink(); ?>">
		<span class="qaf-jcard__main">
			<span class="qaf-jcard__title"><?php the_title(); ?></span>
			<?php if ( $qaf_meta ) : ?>
				<span class="qaf-jcard__meta"><?php echo implode( '<i aria-hidden="true">✦</i>', array_map( 'esc_html', $qaf_meta ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			<?php endif; ?>
		</span>
		<span class="qaf-jcard__side">
			<span class="qaf-jcard__date"><?php echo $qaf_closed ? qaf_text( 'str_closed' ) : qaf_text_f( 'str_posted', get_the_date() ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			<span class="qaf-jcard__btn"><span><?php echo qaf_text( 'str_details' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span><?php echo qaf_icon( 'arrow', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		</span>
	</a>
</li>
