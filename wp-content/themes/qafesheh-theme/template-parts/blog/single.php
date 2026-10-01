<?php
/**
 * Article: centred header, overlapping cover, share | body | contents, tags, related.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$qaf_id      = get_the_ID();
$qaf_content = apply_filters( 'the_content', get_the_content() );
$qaf_toc     = qaf_toc_items( $qaf_content );
$qaf_tags    = get_the_tags();
$qaf_related = qaf_related_posts( $qaf_id );
?>
<header class="qaf-ahead">
	<svg class="qaf-phero__pattern" aria-hidden="true" width="100%" height="100%">
		<defs><pattern id="qaf-art-star" width="128" height="128" patternUnits="userSpaceOnUse"><path d="<?php echo esc_attr( qaf_star_path() ); ?>" fill="none" stroke="currentColor" stroke-width="1"/></pattern></defs>
		<rect width="100%" height="100%" fill="url(#qaf-art-star)"/>
	</svg>
	<div class="qaf-container qaf-ahead__in">
		<div<?php echo qaf_anim( 'fade' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php qaf_breadcrumbs(); ?></div>
		<div<?php echo qaf_anim( 'fade', 100 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php qaf_post_meta_line( $qaf_id, true ); ?></div>
		<h1 class="qaf-ahead__title qaf-foil" data-qaf-anim="blur" style="--qaf-delay:160ms"><?php the_title(); ?></h1>
		<?php if ( has_excerpt() ) : ?>
			<p class="qaf-ahead__lead"<?php echo qaf_anim( 'up', 300 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo esc_html( get_the_excerpt() ); ?></p>
		<?php endif; ?>
	</div>
</header>

<?php if ( has_post_thumbnail() ) : ?>
	<div class="qaf-acover">
		<figure class="qaf-container qaf-acover__in"<?php echo qaf_anim( 'reveal', 200 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
			<span class="qaf-acover__img"><?php the_post_thumbnail( 'qaf-hero', array( 'sizes' => '(max-width: 1440px) 100vw, 1440px', 'loading' => 'eager', 'fetchpriority' => 'high', 'alt' => '' ) ); ?></span>
		</figure>
	</div>
<?php endif; ?>

<article <?php post_class( 'qaf-abody' ); ?>>
	<div class="qaf-container qaf-abody__grid<?php echo $qaf_toc ? '' : ' qaf-abody__grid--notoc'; ?>">
		<aside class="qaf-abody__share">
			<div class="qaf-share" data-qaf-share>
				<p class="qaf-side__label"><?php echo qaf_text( 'str_share' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
				<?php foreach ( qaf_share_links() as $qaf_s ) : ?>
					<a class="qaf-share__btn" href="<?php echo esc_url( $qaf_s[2] ); ?>" target="_blank" rel="noopener noreferrer" data-qaf-nofx><?php echo qaf_icon( $qaf_s[0], 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $qaf_s[1] ); ?></span></a>
				<?php endforeach; ?>
				<button class="qaf-share__btn qaf-share__copy" type="button" data-url="<?php echo esc_url( get_permalink() ); ?>" data-done="<?php echo esc_attr( qaf_opt_i18n( 'str_copied' ) ); ?>"><?php echo qaf_icon( 'link', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo qaf_text( 'str_copy' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span></button>
			</div>
		</aside>

		<div class="qaf-abody__main">
			<div class="qaf-prose qaf-prose--article"<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
				<?php echo $qaf_content; // phpcs:ignore WordPress.Security.EscapeOutput -- filtered post content. ?>
				<?php wp_link_pages(); ?>
			</div>
			<?php if ( $qaf_tags ) : ?>
				<div class="qaf-atags">
					<?php foreach ( $qaf_tags as $qaf_tag ) : ?>
						<a class="qaf-chip" href="<?php echo esc_url( get_tag_link( $qaf_tag ) ); ?>"><?php echo esc_html( $qaf_tag->name ); ?></a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( $qaf_toc ) : ?>
			<aside class="qaf-abody__toc">
				<nav class="qaf-toc" aria-label="<?php echo esc_attr( qaf_opt_i18n( 'str_toc' ) ); ?>" data-qaf-toc>
					<p class="qaf-side__label"><?php echo qaf_text( 'str_toc' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
					<ol>
						<?php foreach ( $qaf_toc as $qaf_i => $qaf_item ) : ?>
							<li class="qaf-toc__item qaf-toc__item--h<?php echo absint( $qaf_item['level'] ); ?><?php echo 0 === $qaf_i ? ' is-active' : ''; ?>"><a href="#<?php echo esc_attr( $qaf_item['id'] ); ?>"><?php echo esc_html( $qaf_item['text'] ); ?></a></li>
						<?php endforeach; ?>
					</ol>
				</nav>
			</aside>
		<?php endif; ?>
	</div>
</article>

<?php if ( $qaf_related ) : ?>
	<section class="qaf-sec qaf-sec--soft qaf-related">
		<div class="qaf-container">
			<p class="qaf-kicker"<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><span class="qaf-kicker__rule" aria-hidden="true"></span><?php echo qaf_text( 'str_related' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
			<div class="qaf-related__grid" data-qaf-stagger>
				<?php foreach ( $qaf_related as $qaf_p ) : ?>
					<a class="qaf-rcard" href="<?php echo esc_url( get_permalink( $qaf_p ) ); ?>"<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
						<span class="qaf-rcard__img"><?php echo get_the_post_thumbnail( $qaf_p, 'qaf-thumb', array( 'sizes' => '(max-width: 900px) 100vw, 33vw', 'alt' => '' ) ); ?></span>
						<?php qaf_post_meta_line( $qaf_p->ID ); ?>
						<span class="qaf-rcard__title"><?php echo esc_html( get_the_title( $qaf_p ) ); ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php endif; ?>
