<?php
/**
 * Sister brands: the other companies, each in its own identity.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$qaf_others = get_posts(
	array(
		'post_type'        => 'qaf_company',
		'posts_per_page'   => 6,
		'post__not_in'     => array( get_the_ID() ),
		'orderby'          => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
		'suppress_filters' => false,
	)
);
if ( ! $qaf_others ) {
	return;
}
?>
<section class="qaf-sec qaf-cosisters">
	<div class="qaf-container">
		<h2 class="qaf-cosisters__title" data-qaf-anim="words"><?php echo qaf_text( 'str_sisters' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
		<div class="qaf-cosisters__grid qaf-cosisters__grid--<?php echo absint( min( 3, count( $qaf_others ) ) ); ?>" data-qaf-stagger>
			<?php foreach ( $qaf_others as $qaf_p ) : ?>
				<?php
				$qaf_logo = qaf_meta( 'logo_dark', $qaf_p->ID ) ? qaf_meta( 'logo_dark', $qaf_p->ID ) : qaf_meta( 'logo', $qaf_p->ID );
				$qaf_line = qaf_meta( 'tagline', $qaf_p->ID ) ? qaf_meta( 'tagline', $qaf_p->ID ) : get_the_excerpt( $qaf_p );
				?>
				<a class="qaf-sister" href="<?php echo esc_url( get_permalink( $qaf_p ) ); ?>" style="--qaf-accent:<?php echo esc_attr( qaf_color_on_light( qaf_company_color( $qaf_p->ID ) ) ); ?>"<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
					<?php if ( $qaf_logo ) : ?>
						<span class="qaf-sister__logo"><img src="<?php echo esc_url( qaf_resolve_url( $qaf_logo ) ); ?>" alt="" width="78" height="78" loading="lazy" decoding="async"></span>
					<?php endif; ?>
					<span class="qaf-sister__body">
						<span class="qaf-sister__name"><?php echo esc_html( get_the_title( $qaf_p ) ); ?></span>
						<span class="qaf-sister__line"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( $qaf_line ), 9, '…' ) ); ?></span>
					</span>
					<?php echo qaf_icon( 'arrow', 18, 'qaf-sister__arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
