<?php
/**
 * Values grid.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$a     = wp_parse_args( $args ?? array(), qaf_seed_page_copy( 'about' )['values'] + array( 'id' => '' ) );
$items = array_values( array_filter( (array) $a['items'], static fn( $i ) => ! empty( $i['title'] ) ) );
?>
<section class="qaf-sec qaf-values"<?php echo $a['id'] ? ' id="' . esc_attr( $a['id'] ) . '"' : ''; ?>>
	<div class="qaf-container">
		<div class="qaf-sechead">
			<?php if ( $a['kicker'] ) : ?>
				<p class="qaf-kicker"<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><span class="qaf-kicker__rule" aria-hidden="true"></span><?php echo esc_html( $a['kicker'] ); ?></p>
			<?php endif; ?>
			<?php if ( $a['title'] ) : ?>
				<h2 class="qaf-sechead__title" data-qaf-anim="words"><?php echo qaf_lines( $a['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
			<?php endif; ?>
		</div>
		<div class="qaf-values__grid" style="--qaf-cols:<?php echo absint( min( 4, max( 1, count( $items ) ) ) ); ?>" data-qaf-stagger>
			<?php foreach ( $items as $item ) : ?>
				<div class="qaf-values__item"<?php echo qaf_anim( 'fade' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
					<?php echo qaf_icon( $item['icon'] ?? 'star', 30, 'qaf-values__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<h3 class="qaf-values__title"><?php echo esc_html( $item['title'] ); ?></h3>
					<p class="qaf-values__text"><?php echo esc_html( $item['text'] ?? '' ); ?></p>
					<span class="qaf-svc__bar" aria-hidden="true"></span>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
