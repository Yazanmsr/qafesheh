<?php
/**
 * How it works: numbered steps bar.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$a     = wp_parse_args( $args ?? array(), array( 'items' => qaf_seed_page_copy( 'complaints' )['steps'], 'id' => '' ) );
$items = array_values( array_filter( (array) $a['items'], static fn( $i ) => ! empty( $i['title'] ) ) );
if ( ! $items ) {
	return;
}
?>
<section class="qaf-stepsbar"<?php echo $a['id'] ? ' id="' . esc_attr( $a['id'] ) . '"' : ''; ?>>
	<ol class="qaf-stepsbar__grid" style="--qaf-cols:<?php echo absint( min( 5, count( $items ) ) ); ?>" data-qaf-stagger>
		<?php foreach ( $items as $i => $item ) : ?>
			<li class="qaf-stepsbar__item"<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
				<span class="qaf-stepsbar__num"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
				<h2 class="qaf-stepsbar__title"><?php echo esc_html( $item['title'] ); ?></h2>
				<?php if ( ! empty( $item['text'] ) ) : ?>
					<p class="qaf-stepsbar__text"><?php echo esc_html( $item['text'] ); ?></p>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ol>
</section>
