<?php
/**
 * 06 — Latest articles as editorial rows.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$a = wp_parse_args(
	$args ?? array(),
	qaf_seed_home()['news'] + array(
		'count'    => 3,
		'category' => 0,
		'read'     => qaf_opt_i18n( 'str_read' ),
		'link_url' => qaf_blog_url(),
		'id'       => '',
	)
);

$qaf_query = array(
	'post_type'           => 'post',
	'posts_per_page'      => max( 1, min( 12, absint( $a['count'] ) ) ),
	'ignore_sticky_posts' => true,
	'suppress_filters'    => false,
);
if ( absint( $a['category'] ) ) {
	$qaf_query['cat'] = absint( $a['category'] );
}
$qaf_posts = get_posts( $qaf_query );

$qaf_items = array();
foreach ( $qaf_posts as $qaf_p ) {
	$qaf_cats    = get_the_category( $qaf_p->ID );
	$qaf_items[] = array(
		'title'   => get_the_title( $qaf_p ),
		'excerpt' => get_the_excerpt( $qaf_p ),
		'image'   => get_post_thumbnail_id( $qaf_p ),
		'date'    => get_the_date( 'd.m.Y', $qaf_p ),
		'iso'     => get_the_date( 'c', $qaf_p ),
		'cat'     => $qaf_cats ? $qaf_cats[0]->name : '',
		'url'     => get_permalink( $qaf_p ),
	);
}
if ( ! $qaf_items ) {
	foreach ( array_slice( qaf_seed_articles(), 0, absint( $a['count'] ) ) as $qaf_s ) {
		$qaf_l       = qaf_seed_lang( $qaf_s );
		$qaf_items[] = array(
			'title'   => $qaf_l['title'],
			'excerpt' => $qaf_l['excerpt'],
			'image'   => $qaf_s['shared']['image'],
			'date'    => wp_date( 'd.m.Y', strtotime( $qaf_s['shared']['date'] ) ),
			'iso'     => gmdate( 'c', strtotime( $qaf_s['shared']['date'] ) ),
			'cat'     => $qaf_l['cat'],
			'url'     => '',
		);
	}
}
?>
<section class="qaf-sec qaf-news"<?php echo $a['id'] ? ' id="' . esc_attr( $a['id'] ) . '"' : ''; ?>>
	<div class="qaf-container qaf-chapter">
		<?php qaf_chapter_rail( $a['num'], $a['label'] ); ?>
		<div class="qaf-chapter__body">
			<div class="qaf-split-head qaf-split-head--link">
				<?php if ( $a['title'] ) : ?>
					<h2 class="qaf-h2" data-qaf-anim="words"><?php echo qaf_lines( $a['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
				<?php endif; ?>
				<div<?php echo qaf_anim( 'fade', 200 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php qaf_arrow_link( $a['link'], $a['link_url'] ); ?></div>
			</div>

			<ul class="qaf-news__list" data-qaf-stagger>
				<?php foreach ( $qaf_items as $qaf_item ) : ?>
					<li class="qaf-news__item"<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
						<?php if ( $qaf_item['url'] ) : ?>
							<a class="qaf-news__cover" href="<?php echo esc_url( $qaf_item['url'] ); ?>" aria-label="<?php echo esc_attr( $qaf_item['title'] ); ?>"></a>
						<?php endif; ?>
						<figure class="qaf-frame qaf-frame--xs qaf-news__thumb">
							<div class="qaf-frame__media"><?php echo qaf_image( $qaf_item['image'], 'qaf-thumb', array( 'sizes' => '(max-width: 900px) 100vw, 200px' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
						</figure>
						<div class="qaf-news__main">
							<h3 class="qaf-news__title"><?php echo esc_html( $qaf_item['title'] ); ?></h3>
							<?php if ( $qaf_item['excerpt'] ) : ?>
								<p class="qaf-news__excerpt"><?php echo esc_html( wp_strip_all_tags( $qaf_item['excerpt'] ) ); ?></p>
							<?php endif; ?>
						</div>
						<p class="qaf-news__meta"><time datetime="<?php echo esc_attr( $qaf_item['iso'] ); ?>" dir="ltr"><?php echo esc_html( $qaf_item['date'] ); ?></time><?php if ( $qaf_item['cat'] ) : ?> — <?php echo esc_html( $qaf_item['cat'] ); ?><?php endif; ?></p>
						<span class="qaf-news__read"><span><?php echo esc_html( $a['read'] ); ?></span><?php echo qaf_icon( 'arrow', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</section>
