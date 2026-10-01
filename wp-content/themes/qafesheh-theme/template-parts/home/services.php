<?php
/**
 * 04 — Services grid (hairline 1px grid).
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$a = wp_parse_args(
	$args ?? array(),
	qaf_seed_home()['services'] + array(
		'count'    => 6,
		'link_url' => qaf_archive_url( 'qaf_service' ),
		'id'       => '',
	)
);

$qaf_posts = get_posts(
	array(
		'post_type'        => 'qaf_service',
		'posts_per_page'   => max( 1, min( 12, absint( $a['count'] ) ) ),
		'orderby'          => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
		'suppress_filters' => false,
	)
);

$qaf_items = array();
if ( $qaf_posts ) {
	foreach ( $qaf_posts as $qaf_p ) {
		$qaf_items[] = array(
			'title' => get_the_title( $qaf_p ),
			'text'  => qaf_meta( 'short', $qaf_p->ID ) ? qaf_meta( 'short', $qaf_p->ID ) : get_the_excerpt( $qaf_p ),
			'icon'  => qaf_meta( 'icon', $qaf_p->ID ),
			'url'   => get_permalink( $qaf_p ),
		);
	}
} else {
	foreach ( array_slice( qaf_seed_services(), 0, absint( $a['count'] ) ) as $qaf_s ) {
		$qaf_l       = qaf_seed_lang( $qaf_s );
		$qaf_items[] = array(
			'title' => $qaf_l['title'],
			'text'  => $qaf_l['meta']['short'] ?? $qaf_l['excerpt'],
			'icon'  => $qaf_s['shared']['meta']['icon'],
			'url'   => '',
		);
	}
}
?>
<section class="qaf-sec qaf-services"<?php echo $a['id'] ? ' id="' . esc_attr( $a['id'] ) . '"' : ''; ?>>
	<div class="qaf-container qaf-chapter">
		<?php qaf_chapter_rail( $a['num'], $a['label'] ); ?>
		<div class="qaf-chapter__body">
			<div class="qaf-split-head qaf-split-head--link">
				<?php if ( $a['title'] ) : ?>
					<h2 class="qaf-h2" data-qaf-anim="words"><?php echo qaf_lines( $a['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
				<?php endif; ?>
				<div<?php echo qaf_anim( 'fade', 200 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php qaf_arrow_link( $a['link'], $a['link_url'] ); ?></div>
			</div>

			<div class="qaf-svc-grid" data-qaf-stagger>
				<?php foreach ( $qaf_items as $qaf_i => $qaf_item ) : ?>
					<?php $qaf_tag = $qaf_item['url'] ? 'a' : 'div'; ?>
					<<?php echo $qaf_tag; // phpcs:ignore WordPress.Security.EscapeOutput ?> class="qaf-svc"<?php echo $qaf_item['url'] ? ' href="' . esc_url( $qaf_item['url'] ) . '"' : ''; ?><?php echo qaf_anim( 'fade' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
						<span class="qaf-svc__top">
							<?php echo qaf_icon( $qaf_item['icon'] ? $qaf_item['icon'] : 'star', 30, 'qaf-svc__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<span class="qaf-svc__num" dir="ltr"><?php echo esc_html( str_pad( (string) ( $qaf_i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						</span>
						<h3 class="qaf-svc__title"><?php echo esc_html( $qaf_item['title'] ); ?></h3>
						<?php if ( $qaf_item['text'] ) : ?>
							<p class="qaf-svc__text"><?php echo esc_html( wp_strip_all_tags( $qaf_item['text'] ) ); ?></p>
						<?php endif; ?>
						<span class="qaf-svc__bar" aria-hidden="true"></span>
					</<?php echo $qaf_tag; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
