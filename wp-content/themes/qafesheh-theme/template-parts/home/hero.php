<?php
/**
 * Hero slider — layered images, per-slide copy, fixed data plinth.
 *
 * Performance
 * - Slide 1 image is the LCP: eager + fetchpriority=high + srcset. Other slides ship as
 *   data-src / data-srcset and are fetched + decoded just before they're shown (never on load).
 * - Motion uses transform / opacity / clip-path on a single layer at a time.
 * - Autoplay pauses off-screen, in hidden tabs, on hover / focus, and is off for reduced motion.
 * - Words are split on the server, so there's no layout shift and no JS needed to render text.
 *
 * Single-slide use (older content) still renders the same design without controls.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$qaf_seed = qaf_seed_home()['hero'];
$a        = wp_parse_args(
	$args ?? array(),
	array(
		'slides'   => array(),
		'stats'    => $qaf_seed['stats'],
		'grid'     => true,
		'autoplay' => true,
		'interval' => 7000,
		'id'       => '',
	)
);

/* Back-compat: a hero saved before slides existed becomes one slide. */
if ( ! $a['slides'] ) {
	$a['slides'] = ! empty( $args['title'] ) || ! empty( $args['image'] )
		? array( array_intersect_key( $args, array_flip( array( 'image', 'position', 'eyebrow', 'title', 'text', 'btn1', 'btn1_url', 'btn2', 'btn2_url' ) ) ) )
		: $qaf_seed['slides'];
}
$qaf_slides = array_values( array_filter( (array) $a['slides'], static fn( $s ) => ! empty( $s['title'] ) || ! empty( $s['image'] ) ) );
$qaf_count  = count( $qaf_slides );
$qaf_multi  = $qaf_count > 1;
$qaf_stats  = array_filter( (array) $a['stats'], static fn( $s ) => ! empty( $s['num'] ) || ! empty( $s['label'] ) );
$qaf_ivl    = max( 3500, min( 15000, absint( $a['interval'] ) ) );
?>
<section class="qaf-hero<?php echo $qaf_multi ? ' qaf-hero--slider' : ''; ?>"<?php echo $a['id'] ? ' id="' . esc_attr( $a['id'] ) . '"' : ''; ?> data-qaf-hero<?php echo $qaf_multi ? ' data-qaf-slider data-interval="' . esc_attr( (string) $qaf_ivl ) . '" data-autoplay="' . ( $a['autoplay'] ? '1' : '0' ) . '" aria-roledescription="carousel"' : ''; ?> style="--qaf-slide-ms:<?php echo esc_attr( (string) $qaf_ivl ); ?>ms">

	<div class="qaf-hero__slides" aria-hidden="true">
		<?php foreach ( $qaf_slides as $qaf_i => $qaf_s ) : ?>
			<?php
			$qaf_pos   = preg_replace( '/[^a-z0-9% .]/i', '', (string) ( $qaf_s['position'] ?? 'center' ) );
			$qaf_image = $qaf_s['image'] ?? '';
			?>
			<div class="qaf-hero__media<?php echo 0 === $qaf_i ? ' is-active' : ''; ?>" data-slide="<?php echo absint( $qaf_i ); ?>">
				<?php
				if ( 0 === $qaf_i ) {
					echo qaf_image( $qaf_image, 'qaf-hero', array( 'class' => 'qaf-hero__img', 'loading' => 'eager', 'fetchpriority' => 'high', 'sizes' => '100vw', 'style' => 'object-position:' . $qaf_pos ) ); // phpcs:ignore WordPress.Security.EscapeOutput
				} else {
					$qaf_src    = qaf_image_url( $qaf_image, 'qaf-hero' );
					$qaf_att    = is_numeric( $qaf_image ) ? (int) $qaf_image : qaf_imported_media_id( (string) $qaf_image );
					$qaf_srcset = $qaf_att ? (string) wp_get_attachment_image_srcset( $qaf_att, 'qaf-hero' ) : '';
					printf(
						'<img class="qaf-hero__img" alt="" decoding="async" data-src="%1$s"%2$s sizes="100vw" style="object-position:%3$s">',
						esc_url( $qaf_src ),
						$qaf_srcset ? ' data-srcset="' . esc_attr( $qaf_srcset ) . '"' : '',
						esc_attr( $qaf_pos )
					);
				}
				?>
			</div>
		<?php endforeach; ?>
		<span class="qaf-hero__sweep" aria-hidden="true"></span>
	</div>

	<div class="qaf-hero__shade" aria-hidden="true"></div>
	<?php if ( $a['grid'] ) : ?>
		<div class="qaf-lines" aria-hidden="true"></div>
	<?php endif; ?>

	<div class="qaf-container qaf-hero__in">
		<div class="qaf-hero__stage">
			<?php foreach ( $qaf_slides as $qaf_i => $qaf_s ) : ?>
				<?php
				$qaf_first = 0 === $qaf_i;
				$qaf_tag   = $qaf_first ? 'h1' : 'h2';
				?>
				<div class="qaf-hero__content<?php echo $qaf_first ? ' is-active' : ''; ?>" data-slide="<?php echo absint( $qaf_i ); ?>"<?php echo $qaf_multi ? ' role="group" aria-roledescription="slide" aria-label="' . esc_attr( ( $qaf_i + 1 ) . ' / ' . $qaf_count ) . '"' : ''; ?><?php echo $qaf_first ? '' : ' aria-hidden="true" inert'; ?>>
					<?php if ( ! empty( $qaf_s['eyebrow'] ) ) : ?>
						<p class="qaf-eyebrow qaf-hero__eyebrow">
							<span class="qaf-eyebrow__rule" aria-hidden="true"></span>
							<span dir="auto"><?php echo esc_html( $qaf_s['eyebrow'] ); ?></span>
						</p>
					<?php endif; ?>

					<?php if ( ! empty( $qaf_s['title'] ) ) : ?>
						<<?php echo $qaf_tag; // phpcs:ignore WordPress.Security.EscapeOutput ?> class="qaf-hero__title"><?php echo qaf_split_words( $qaf_s['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></<?php echo $qaf_tag; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
					<?php endif; ?>

					<div class="qaf-dbl-rule qaf-dbl-rule--hero qaf-hero__rule" aria-hidden="true"><span></span><span></span></div>

					<?php if ( ! empty( $qaf_s['text'] ) ) : ?>
						<p class="qaf-hero__text"><?php echo esc_html( $qaf_s['text'] ); ?></p>
					<?php endif; ?>

					<?php if ( ! empty( $qaf_s['btn1'] ) || ! empty( $qaf_s['btn2'] ) ) : ?>
						<div class="qaf-hero__actions">
							<?php qaf_button( $qaf_s['btn1'] ?? '', $qaf_s['btn1_url'] ?? '', 'solid', 'qaf-btn--lg' ); ?>
							<?php qaf_button( $qaf_s['btn2'] ?? '', $qaf_s['btn2_url'] ?? '', 'outline', 'qaf-btn--lg' ); ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>

		<?php if ( $qaf_multi ) : ?>
			<div class="qaf-hero__controls">
				<div class="qaf-hero__counter" aria-live="polite" aria-atomic="true">
					<span class="qaf-hero__num"><span class="qaf-hero__digits" data-current>01</span></span>
					<span class="qaf-hero__of">/</span>
					<span class="qaf-hero__total"><?php echo esc_html( str_pad( (string) $qaf_count, 2, '0', STR_PAD_LEFT ) ); ?></span>
				</div>
				<div class="qaf-hero__bars" role="tablist" aria-label="slides">
					<?php for ( $qaf_n = 0; $qaf_n < $qaf_count; $qaf_n++ ) : ?>
						<button type="button" class="qaf-hero__bar<?php echo 0 === $qaf_n ? ' is-active' : ''; ?>" role="tab" aria-selected="<?php echo 0 === $qaf_n ? 'true' : 'false'; ?>" aria-label="<?php echo esc_attr( ( $qaf_n + 1 ) . ' / ' . $qaf_count ); ?>" data-go="<?php echo absint( $qaf_n ); ?>"><span></span></button>
					<?php endfor; ?>
				</div>
				<div class="qaf-hero__arrows">
					<button type="button" class="qaf-hero__arrow" data-dir="-1" aria-label="<?php echo esc_attr( qaf_opt_i18n( 'str_prev' ) ); ?>"><?php echo qaf_icon( 'arrow', 18, 'qaf-flip' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
					<button type="button" class="qaf-hero__arrow" data-dir="1" aria-label="<?php echo esc_attr( qaf_opt_i18n( 'str_next' ) ); ?>"><?php echo qaf_icon( 'arrow', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( $qaf_stats ) : ?>
			<dl class="qaf-plinth" style="--qaf-cols:<?php echo absint( min( 4, count( $qaf_stats ) ) ); ?>"<?php echo qaf_anim( 'plinth', 760 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
				<?php foreach ( $qaf_stats as $qaf_stat ) : ?>
					<div class="qaf-plinth__item">
						<dt class="qaf-plinth__num" dir="ltr"><span data-qaf-count="<?php echo esc_attr( $qaf_stat['num'] ?? '' ); ?>"><?php echo esc_html( $qaf_stat['num'] ?? '' ); ?></span><?php if ( ! empty( $qaf_stat['suffix'] ) ) : ?><em><?php echo esc_html( $qaf_stat['suffix'] ); ?></em><?php endif; ?></dt>
						<dd class="qaf-plinth__label"><?php echo esc_html( $qaf_stat['label'] ?? '' ); ?></dd>
					</div>
				<?php endforeach; ?>
			</dl>
		<?php endif; ?>
	</div>
</section>
