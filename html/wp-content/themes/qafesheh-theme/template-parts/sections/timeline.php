<?php
/**
 * Timeline: dashed track with markers that light up one by one.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$a     = wp_parse_args( $args ?? array(), qaf_seed_page_copy( 'about' )['timeline'] + array( 'id' => '' ) );
$items = array_values( array_filter( (array) $a['items'], static fn( $i ) => ! empty( $i['title'] ) ) );
if ( ! $items ) {
	return;
}
?>
<section class="qaf-sec qaf-sec--soft qaf-timeline"<?php echo $a['id'] ? ' id="' . esc_attr( $a['id'] ) . '"' : ''; ?> data-qaf-timeline>
	<div class="qaf-container">
		<div class="qaf-sechead">
			<?php if ( $a['kicker'] ) : ?>
				<p class="qaf-kicker"<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><span class="qaf-kicker__rule" aria-hidden="true"></span><?php echo esc_html( $a['kicker'] ); ?></p>
			<?php endif; ?>
			<?php if ( $a['title'] ) : ?>
				<h2 class="qaf-sechead__title" data-qaf-anim="words"><?php echo qaf_lines( $a['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
			<?php endif; ?>
		</div>
		<ol class="qaf-timeline__list" style="--qaf-cols:<?php echo absint( min( 6, count( $items ) ) ); ?>">
			<span class="qaf-timeline__track" aria-hidden="true"><span class="qaf-timeline__fill"></span></span>
			<?php foreach ( $items as $i => $item ) : ?>
				<li class="qaf-timeline__item" style="--qaf-i:<?php echo absint( $i ); ?>">
					<span class="qaf-timeline__dot" aria-hidden="true"></span>
					<span class="qaf-timeline__year"><?php echo esc_html( $item['year'] ?? '' ); ?></span>
					<h3 class="qaf-timeline__title"><?php echo esc_html( $item['title'] ); ?></h3>
					<?php if ( ! empty( $item['text'] ) ) : ?>
						<p class="qaf-timeline__text"><?php echo esc_html( $item['text'] ); ?></p>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>
