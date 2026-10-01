<?php
/**
 * 07 — Branches: list + live map. Selecting a branch moves the map (no API key, lazy iframe).
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$a = wp_parse_args(
	$args ?? array(),
	qaf_seed_home()['branches'] + array(
		'count' => 5,
		'brand' => '',
		'id'    => '',
	)
);

$qaf_query = array(
	'post_type'        => 'qaf_branch',
	'posts_per_page'   => max( 1, min( 20, absint( $a['count'] ) ) ),
	'orderby'          => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
	'suppress_filters' => false,
);
if ( $a['brand'] ) {
	$qaf_query['tax_query'] = array( array( 'taxonomy' => 'qaf_brand', 'field' => 'slug', 'terms' => sanitize_title( $a['brand'] ) ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
}

$qaf_items = array();
foreach ( get_posts( $qaf_query ) as $qaf_p ) {
	$qaf_items[] = array(
		'name'    => qaf_meta( 'short', $qaf_p->ID ) ? qaf_meta( 'short', $qaf_p->ID ) : get_the_title( $qaf_p ),
		'hours'   => qaf_meta( 'hours', $qaf_p->ID ),
		'address' => qaf_meta( 'address', $qaf_p->ID ),
		'lat'     => qaf_meta( 'lat', $qaf_p->ID ),
		'lng'     => qaf_meta( 'lng', $qaf_p->ID ),
		'map'     => qaf_meta( 'map_url', $qaf_p->ID ),
	);
}
if ( ! $qaf_items ) {
	foreach ( array_slice( qaf_seed_branches(), 0, absint( $a['count'] ) ) as $qaf_s ) {
		$qaf_l       = qaf_seed_lang( $qaf_s );
		$qaf_items[] = array(
			'name'    => $qaf_l['meta']['short'],
			'hours'   => $qaf_l['meta']['hours'],
			'address' => $qaf_l['meta']['address'],
			'lat'     => '',
			'lng'     => '',
			'map'     => $qaf_s['shared']['meta']['map_url'],
		);
	}
}

/**
 * Embed URL for a branch.
 *
 * @param array $b Branch.
 * @return string
 */
$qaf_embed = static function ( $b ) {
	$q = ( $b['lat'] && $b['lng'] ) ? $b['lat'] . ',' . $b['lng'] : $b['address'] . ', Jordan';
	return 'https://maps.google.com/maps?q=' . rawurlencode( $q ) . '&z=15&output=embed&hl=' . rawurlencode( qaf_current_lang() );
};
?>
<section class="qaf-branches"<?php echo $a['id'] ? ' id="' . esc_attr( $a['id'] ) . '"' : ''; ?> data-qaf-branches>
	<div class="qaf-branches__content">
		<div class="qaf-branches__head">
			<?php qaf_chapter_rail( $a['num'], $a['label'], 'qaf-rail--inline' ); ?>
			<?php if ( $a['title'] ) : ?>
				<h2 class="qaf-h2" data-qaf-anim="words"><?php echo qaf_lines( $a['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
			<?php endif; ?>
		</div>

		<ul class="qaf-branch-list" data-qaf-stagger>
			<?php foreach ( $qaf_items as $qaf_i => $qaf_b ) : ?>
				<li<?php echo qaf_anim( 'start' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
					<button type="button" class="qaf-branch<?php echo 0 === $qaf_i ? ' is-active' : ''; ?>" data-map="<?php echo esc_url( $qaf_embed( $qaf_b ) ); ?>" aria-pressed="<?php echo 0 === $qaf_i ? 'true' : 'false'; ?>">
						<span class="qaf-branch__name"><?php echo esc_html( $qaf_b['name'] ); ?></span>
						<span class="qaf-branch__hours" dir="auto"><?php echo esc_html( $qaf_b['hours'] ); ?></span>
					</button>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>

	<div class="qaf-branches__map"<?php echo qaf_anim( 'fade', 200 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
		<?php if ( $qaf_items ) : ?>
			<iframe class="qaf-branches__frame" title="<?php echo esc_attr( $a['label'] ); ?>" data-src="<?php echo esc_url( $qaf_embed( $qaf_items[0] ) ); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" tabindex="-1"></iframe>
			<?php if ( $qaf_items[0]['map'] ) : ?>
				<a class="qaf-branches__open" href="<?php echo esc_url( $qaf_items[0]['map'] ); ?>" target="_blank" rel="noopener" data-qaf-nofx><?php echo qaf_icon( 'external', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo qaf_text( 'str_map' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span></a>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</section>
