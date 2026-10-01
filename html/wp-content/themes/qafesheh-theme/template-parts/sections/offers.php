<?php
/**
 * Offers from the three houses — quiet, square, editorial.
 * Each card carries its company's colour as a hairline, the discount set in the serif,
 * a restrained countdown, and one link to that company's store.
 * Renders nothing when no offer is running.
 * args: num, label, title, text, limit, id.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'qaf_offers_live' ) ) {
	return;
}
$a      = wp_parse_args( $args ?? array(), array( 'num' => '', 'label' => '', 'title' => '', 'text' => '', 'limit' => 3, 'id' => 'offers' ) );
$en     = 'en' === qaf_current_lang();
$offers = qaf_offers_live( max( 1, (int) $a['limit'] ) );
if ( ! $offers ) {
	return;
}
$a['label'] = $a['label'] ? $a['label'] : ( $en ? 'Current offers' : 'عروض الشركات' );
$a['title'] = $a['title'] ? $a['title'] : ( $en ? "From our three houses,\nfor a limited time" : "من بيوتنا الثلاثة،\nلفترة محدودة" );
$units      = $en ? array( 'days', 'hrs', 'min', 'sec' ) : array( 'يوم', 'ساعة', 'دقيقة', 'ثانية' );
$solo       = 1 === count( $offers );
?>
<section class="qaf-sec qaf-offers<?php echo $solo ? ' is-solo' : ''; ?>"<?php echo $a['id'] ? ' id="' . esc_attr( $a['id'] ) . '"' : ''; ?>>
	<div class="qaf-container">
		<div class="qaf-chapter qaf-chapter--head">
			<?php qaf_chapter_rail( $a['num'], $a['label'] ); ?>
			<div class="qaf-split-head">
				<h2 class="qaf-h2" data-qaf-anim="words"><?php echo qaf_lines( $a['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
				<?php if ( $a['text'] ) : ?>
					<p class="qaf-split-head__text"<?php echo qaf_anim( 'up', 160 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo esc_html( $a['text'] ); ?></p>
				<?php endif; ?>
			</div>
		</div>

		<div class="qaf-offers__grid" style="--n:<?php echo absint( count( $offers ) ); ?>">
			<?php foreach ( $offers as $i => $o ) : ?>
				<?php
				$color = $o['company'] ? qaf_company_color( $o['company'] ) : '';
				$url   = qaf_offer_url( $o );
				$ext   = 0 !== strpos( $url, home_url() );
				?>
				<article class="qaf-offer" style="--c:<?php echo esc_attr( $color ? $color : 'var(--qaf-primary)' ); ?>"<?php echo qaf_anim( 'up', $i * 90 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
					<a class="qaf-offer__media" href="<?php echo esc_url( $url ); ?>"<?php echo $ext ? ' target="_blank" rel="noopener"' : ''; ?> tabindex="-1" aria-hidden="true">
						<?php
						echo qaf_image( // phpcs:ignore WordPress.Security.EscapeOutput
							get_post_thumbnail_id( $o['id'] ),
							'large',
							array( 'class' => 'qaf-offer__img', 'alt' => '', 'loading' => 'lazy' )
						);
						?>
						<?php if ( $o['badge'] ) : ?>
							<span class="qaf-offer__badge"><?php echo esc_html( $o['badge'] ); ?></span>
						<?php endif; ?>
					</a>
					<div class="qaf-offer__body">
						<?php if ( $o['company'] ) : ?>
							<p class="qaf-offer__house"><i aria-hidden="true"></i><span><?php echo esc_html( get_the_title( $o['company'] ) ); ?></span><?php $sector = (string) qaf_meta( 'sector', $o['company'] ); ?><?php if ( $sector ) : ?><small><?php echo esc_html( $sector ); ?></small><?php endif; ?></p>
						<?php endif; ?>
						<?php if ( $o['amount'] ) : ?>
							<p class="qaf-offer__amount"><?php echo esc_html( $o['amount'] ); ?></p>
						<?php endif; ?>
						<h3 class="qaf-offer__title"><a href="<?php echo esc_url( $url ); ?>"<?php echo $ext ? ' target="_blank" rel="noopener"' : ''; ?>><?php echo esc_html( get_the_title( $o['id'] ) ); ?></a></h3>
						<?php if ( has_excerpt( $o['id'] ) ) : ?>
							<p class="qaf-offer__text"><?php echo esc_html( get_the_excerpt( $o['id'] ) ); ?></p>
						<?php endif; ?>
						<div class="qaf-offer__foot">
							<?php if ( $o['end'] && $o['countdown'] ) : ?>
								<div class="qaf-cd" data-qaf-countdown="<?php echo esc_attr( (string) qaf_offer_ts( $o['end'] ) ); ?>" role="timer" aria-label="<?php echo esc_attr( qaf_offer_time_left( $o ) ); ?>">
									<?php foreach ( array( 'd', 'h', 'm', 's' ) as $k => $unit ) : ?>
										<span class="qaf-cd__cell"><b data-cd="<?php echo esc_attr( $unit ); ?>">00</b><small><?php echo esc_html( $units[ $k ] ); ?></small></span>
									<?php endforeach; ?>
								</div>
							<?php else : ?>
								<p class="qaf-offer__left"><?php echo esc_html( qaf_offer_time_left( $o ) ); ?></p>
							<?php endif; ?>
							<a class="qaf-offer__cta" href="<?php echo esc_url( $url ); ?>"<?php echo $ext ? ' target="_blank" rel="noopener"' : ''; ?>><span><?php echo esc_html( $o['cta'] ? $o['cta'] : ( $en ? 'Shop the offer' : 'تسوّق العرض' ) ); ?></span><?php echo qaf_icon( 'arrow', 16, 'qaf-dir' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
						</div>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
