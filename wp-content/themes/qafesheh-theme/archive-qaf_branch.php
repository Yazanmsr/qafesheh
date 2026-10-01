<?php
/**
 * Branches: hero, brand filter, branch list + sticky live map.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

get_header();

get_template_part(
	'template-parts/header/page-hero',
	null,
	array(
		'title' => qaf_opt_i18n( 'br_title' ),
		'text'  => qaf_opt_i18n( 'br_text' ),
	)
);

$qaf_branches = get_posts(
	array(
		'post_type'        => 'qaf_branch',
		'posts_per_page'   => 50,
		'orderby'          => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
		'suppress_filters' => false,
	)
);
$qaf_first = $qaf_branches ? $qaf_branches[0] : null;
?>
<div class="qaf-brwrap" data-qaf-brfinder>
	<?php get_template_part( 'template-parts/branches/filter' ); ?>

	<div class="qaf-brgrid">
		<div class="qaf-brlist">
			<?php if ( $qaf_branches ) : ?>
				<ul class="qaf-brlist__items" data-qaf-stagger>
					<?php foreach ( $qaf_branches as $qaf_i => $qaf_b ) : ?>
						<?php get_template_part( 'template-parts/branches/card', null, array( 'post' => $qaf_b, 'active' => 0 === $qaf_i ) ); ?>
					<?php endforeach; ?>
				</ul>
				<p class="qaf-brlist__empty" hidden><?php echo qaf_text( 'str_no_results' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
			<?php else : ?>
				<?php get_template_part( 'template-parts/content/content', 'none' ); ?>
			<?php endif; ?>
		</div>

		<div class="qaf-brmap">
			<div class="qaf-brmap__sticky">
				<?php if ( $qaf_first ) : ?>
					<iframe class="qaf-brmap__frame" title="<?php echo esc_attr( qaf_opt_i18n( 'br_title' ) ); ?>" data-src="<?php echo esc_url( qaf_branch_embed( $qaf_first->ID ) ); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
					<div class="qaf-brmap__badge" aria-live="polite">
						<?php echo qaf_icon( 'pin', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<span class="qaf-brmap__name"><?php echo esc_html( get_the_title( $qaf_first ) ); ?></span>
						<a class="qaf-brmap__open" href="<?php echo esc_url( qaf_meta( 'map_url', $qaf_first->ID ) ); ?>" target="_blank" rel="noopener" data-qaf-nofx aria-label="<?php echo esc_attr( qaf_opt_i18n( 'str_map' ) ); ?>"><?php echo qaf_icon( 'external', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
<?php
get_footer();
