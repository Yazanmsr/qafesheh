<?php
/**
 * Brands strip: logo, name in brand colour, sector — links to each company.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$a     = wp_parse_args( $args ?? array(), array( 'id' => '' ) );
$posts = get_posts( array( 'post_type' => 'qaf_company', 'posts_per_page' => 6, 'orderby' => array( 'menu_order' => 'ASC' ), 'suppress_filters' => false ) );
if ( ! $posts ) {
	return;
}
?>
<section class="qaf-bstrip"<?php echo $a['id'] ? ' id="' . esc_attr( $a['id'] ) . '"' : ''; ?>>
	<div class="qaf-bstrip__grid" style="--qaf-cols:<?php echo absint( count( $posts ) ); ?>" data-qaf-stagger>
		<?php foreach ( $posts as $p ) : ?>
			<?php
			$color = qaf_company_color( $p->ID );
			$logo  = qaf_meta( 'logo', $p->ID );
			$spec  = explode( '·', qaf_meta( 'specialty', $p->ID ) );
			?>
			<a class="qaf-bstrip__item" href="<?php echo esc_url( get_permalink( $p ) ); ?>" style="--qaf-accent:<?php echo esc_attr( $color ); ?>"<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
				<?php if ( $logo ) : ?>
					<span class="qaf-bstrip__logo"><img src="<?php echo esc_url( qaf_resolve_url( $logo ) ); ?>" alt="" width="66" height="66" loading="lazy" decoding="async"></span>
				<?php endif; ?>
				<span class="qaf-bstrip__name"><?php echo esc_html( get_the_title( $p ) ); ?></span>
				<span class="qaf-bstrip__sub"><?php echo esc_html( trim( $spec[0] ) ); ?></span>
			</a>
		<?php endforeach; ?>
	</div>
</section>
