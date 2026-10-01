<?php
/**
 * 02 — Business divisions: three equal-height brand cards.
 * Reads the Companies content type; falls back to seed data on a fresh install.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$a = wp_parse_args(
	$args ?? array(),
	qaf_seed_home()['companies'] + array(
		'count' => 3,
		'head'  => true,
		'plain' => false,
		'id'    => 'companies',
	)
);

$qaf_posts = get_posts(
	array(
		'post_type'        => 'qaf_company',
		'posts_per_page'   => max( 1, min( 12, absint( $a['count'] ) ) ),
		'orderby'          => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
		'suppress_filters' => false,
	)
);

$qaf_cards = array();
if ( $qaf_posts ) {
	foreach ( $qaf_posts as $qaf_p ) {
		$qaf_cards[] = array(
			'title'     => get_the_title( $qaf_p ),
			'url'       => get_permalink( $qaf_p ),
			'excerpt'   => get_the_excerpt( $qaf_p ),
			'image'     => get_post_thumbnail_id( $qaf_p ),
			'img_pos'   => qaf_meta( 'img_pos', $qaf_p->ID ),
			'sector'    => qaf_meta( 'sector', $qaf_p->ID ),
			'logo'      => qaf_meta( 'logo', $qaf_p->ID ),
			'color'     => qaf_company_color( $qaf_p->ID ),
			'specialty' => qaf_meta( 'specialty', $qaf_p->ID ),
			'markets'   => qaf_meta( 'markets', $qaf_p->ID ),
			'points'    => qaf_meta( 'points', $qaf_p->ID ),
		);
	}
} else {
	foreach ( qaf_seed_companies() as $qaf_seed ) {
		$qaf_l       = qaf_seed_lang( $qaf_seed );
		$qaf_cards[] = array(
			'title'   => $qaf_l['title'],
			'url'     => '',
			'excerpt' => $qaf_l['excerpt'],
			'image'   => $qaf_seed['shared']['image'],
		) + $qaf_seed['shared']['meta'] + $qaf_l['meta'];
	}
}
?>
<section class="qaf-sec qaf-sec--ink qaf-companies<?php echo $a['plain'] ? ' qaf-companies--plain' : ''; ?>"<?php echo $a['id'] ? ' id="' . esc_attr( $a['id'] ) . '"' : ''; ?>>
	<div class="qaf-lines" aria-hidden="true"></div>
	<div class="qaf-container">
		<?php if ( $a['head'] ) : ?>
		<div class="qaf-chapter qaf-chapter--head">
			<?php qaf_chapter_rail( $a['num'], $a['label'] ); ?>
			<div class="qaf-split-head">
				<?php if ( $a['title'] ) : ?>
					<h2 class="qaf-h2" data-qaf-anim="words"><?php echo qaf_lines( $a['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
				<?php endif; ?>
				<?php if ( $a['text'] ) : ?>
					<p class="qaf-split-head__text"<?php echo qaf_anim( 'up', 160 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo esc_html( $a['text'] ); ?></p>
				<?php endif; ?>
			</div>
		</div>
		<?php endif; ?>

		<div class="qaf-brands" data-qaf-stagger>
			<?php foreach ( $qaf_cards as $qaf_i => $qaf_c ) : ?>
				<?php
				$qaf_tag   = $qaf_c['url'] ? 'a' : 'div';
				$qaf_color = sanitize_hex_color( $qaf_c['color'] ) ? sanitize_hex_color( $qaf_c['color'] ) : qaf_opt( 'c_primary' );
				?>
				<<?php echo $qaf_tag; // phpcs:ignore WordPress.Security.EscapeOutput ?> class="qaf-brand" style="--qaf-accent:<?php echo esc_attr( $qaf_color ); ?>"<?php echo $qaf_c['url'] ? ' href="' . esc_url( $qaf_c['url'] ) . '"' : ''; ?><?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
					<span class="qaf-brand__glow" aria-hidden="true"></span>
					<div class="qaf-brand__frame">
						<div class="qaf-brand__media">
							<?php
							echo qaf_image( // phpcs:ignore WordPress.Security.EscapeOutput
								$qaf_c['image'],
								'qaf-card',
								array(
									'sizes' => '(max-width: 900px) 100vw, 33vw',
									'style' => 'object-position:' . preg_replace( '/[^a-z ]/i', '', (string) ( $qaf_c['img_pos'] ? $qaf_c['img_pos'] : 'center' ) ),
								)
							);
							?>
						</div>
					</div>
					<div class="qaf-brand__body">
						<div class="qaf-brand__head">
							<div class="qaf-brand__names">
								<h3 class="qaf-brand__title"><?php echo esc_html( $qaf_c['title'] ); ?></h3>
								<?php if ( $qaf_c['sector'] ) : ?>
									<span class="qaf-brand__sector" dir="ltr"><?php echo esc_html( $qaf_c['sector'] ); ?></span>
								<?php endif; ?>
							</div>
							<?php if ( $qaf_c['logo'] ) : ?>
								<span class="qaf-brand__logo"><img src="<?php echo esc_url( qaf_resolve_url( $qaf_c['logo'] ) ); ?>" alt="" loading="lazy" decoding="async" width="62" height="62"></span>
							<?php endif; ?>
						</div>
						<span class="qaf-brand__rule" aria-hidden="true"></span>
						<?php if ( $qaf_c['excerpt'] ) : ?>
							<p class="qaf-brand__text"><?php echo esc_html( wp_strip_all_tags( $qaf_c['excerpt'] ) ); ?></p>
						<?php endif; ?>
						<dl class="qaf-specs qaf-specs--dark">
							<?php
							foreach ( array( 'specialty' => 'str_specialty', 'markets' => 'str_markets', 'points' => 'str_points' ) as $qaf_k => $qaf_str ) :
								if ( empty( $qaf_c[ $qaf_k ] ) ) {
									continue;
								}
								?>
								<div class="qaf-specs__row"><dt><?php echo qaf_text( $qaf_str ); // phpcs:ignore WordPress.Security.EscapeOutput ?></dt><dd><?php echo esc_html( $qaf_c[ $qaf_k ] ); ?></dd></div>
							<?php endforeach; ?>
						</dl>
						<div class="qaf-brand__foot">
							<span class="qaf-link qaf-link--light"><span><?php echo qaf_text( 'str_brand_page' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span><?php echo qaf_icon( 'arrow', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
							<span class="qaf-brand__index" dir="ltr"><?php echo esc_html( str_pad( (string) ( $qaf_i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						</div>
					</div>
				</<?php echo $qaf_tag; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
			<?php endforeach; ?>
		</div>
	</div>
</section>
