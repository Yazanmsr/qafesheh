<?php
/**
 * Company hero.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$qaf_id      = get_the_ID();
$qaf_title   = get_the_title();
$qaf_logo    = qaf_meta( 'logo' );
$qaf_sub     = trim( qaf_meta( 'en_name' ) . ' — ' . qaf_meta( 'sector' ), ' —' );
$qaf_tagline = qaf_meta( 'tagline' ) ? qaf_meta( 'tagline' ) : get_the_excerpt();
$qaf_shop    = qaf_meta( 'shop_url' );
$qaf_catalog = qaf_meta( 'catalog' );
?>
<section class="qaf-cohero" data-qaf-hero>
	<div class="qaf-cohero__media qaf-hero__media" aria-hidden="true">
		<?php
		if ( has_post_thumbnail() ) {
			the_post_thumbnail( 'qaf-hero', array( 'class' => 'qaf-hero__img', 'loading' => 'eager', 'fetchpriority' => 'high', 'sizes' => '100vw', 'alt' => '' ) );
		}
		?>
	</div>
	<div class="qaf-cohero__shade" aria-hidden="true"></div>
	<svg class="qaf-phero__pattern" aria-hidden="true" width="100%" height="100%">
		<defs><pattern id="qaf-co-star" width="128" height="128" patternUnits="userSpaceOnUse"><path d="<?php echo esc_attr( qaf_star_path() ); ?>" fill="none" stroke="currentColor" stroke-width="1"/></pattern></defs>
		<rect width="100%" height="100%" fill="url(#qaf-co-star)"/>
	</svg>

	<div class="qaf-container qaf-cohero__in">
		<div<?php echo qaf_anim( 'fade' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php qaf_breadcrumbs(); ?></div>

		<div class="qaf-cohero__id">
			<?php if ( $qaf_logo ) : ?>
				<span class="qaf-cohero__logo"<?php echo qaf_anim( 'zoom', 60 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><img src="<?php echo esc_url( qaf_resolve_url( $qaf_logo ) ); ?>" alt="<?php echo esc_attr( $qaf_title ); ?>" width="108" height="108" fetchpriority="high"></span>
			<?php endif; ?>
			<div class="qaf-cohero__names">
				<h1 class="qaf-cohero__title qaf-foil" data-qaf-anim="blur" style="--qaf-delay:140ms"><?php echo esc_html( $qaf_title ); ?></h1>
				<?php if ( $qaf_sub ) : ?>
					<p class="qaf-cohero__sub" dir="ltr"<?php echo qaf_anim( 'fade', 320 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo esc_html( $qaf_sub ); ?></p>
				<?php endif; ?>
			</div>
		</div>

		<span class="qaf-cohero__rule" aria-hidden="true"<?php echo qaf_anim( 'line', 380 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><span></span></span>

		<?php if ( $qaf_tagline ) : ?>
			<p class="qaf-cohero__text"<?php echo qaf_anim( 'up', 440 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo esc_html( wp_strip_all_tags( $qaf_tagline ) ); ?></p>
		<?php endif; ?>

		<?php if ( $qaf_shop || $qaf_catalog ) : ?>
			<div class="qaf-cohero__actions"<?php echo qaf_anim( 'up', 540 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
				<?php if ( $qaf_shop ) : ?>
					<a class="qaf-btn qaf-btn--solid qaf-btn--lg" href="<?php echo esc_url( $qaf_shop ); ?>" target="_blank" rel="noopener" data-qaf-nofx><span class="qaf-btn__label"><?php echo qaf_text_f( 'str_visit_shop', $qaf_title ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span><?php echo qaf_icon( 'external', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
				<?php endif; ?>
				<?php if ( $qaf_catalog ) : ?>
					<a class="qaf-btn qaf-btn--outline qaf-btn--lg" href="<?php echo esc_url( $qaf_catalog ); ?>" download data-qaf-nofx><span class="qaf-btn__label"><?php echo qaf_text( 'str_catalog' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span></a>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
<?php if ( qaf_opt( 'footer_stitch' ) ) : ?>
	<div class="qaf-stitch qaf-stitch--band" aria-hidden="true"></div>
<?php endif; ?>
