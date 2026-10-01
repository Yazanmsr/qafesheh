<?php
/**
 * Story: arched image | kicker, title, two paragraphs.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$a = wp_parse_args( $args ?? array(), qaf_seed_page_copy( 'about' )['story'] + array( 'id' => '' ) );
?>
<section class="qaf-story"<?php echo $a['id'] ? ' id="' . esc_attr( $a['id'] ) . '"' : ''; ?>>
	<div class="qaf-story__media">
		<figure class="qaf-story__frame"<?php echo qaf_anim( 'reveal' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
			<span class="qaf-story__img"><?php echo qaf_image( $a['image'], 'qaf-wide', array( 'sizes' => '(max-width: 900px) 100vw, 50vw', 'style' => 'object-position:right center' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		</figure>
		<svg class="qaf-story__star" viewBox="0 0 128 128" width="120" height="120" aria-hidden="true"><path d="<?php echo esc_attr( qaf_star_path() ); ?>" fill="none" stroke="currentColor" stroke-width="1"/></svg>
	</div>
	<div class="qaf-story__copy">
		<?php if ( $a['kicker'] ) : ?>
			<p class="qaf-kicker"<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><span class="qaf-kicker__rule" aria-hidden="true"></span><?php echo esc_html( $a['kicker'] ); ?></p>
		<?php endif; ?>
		<?php if ( $a['title'] ) : ?>
			<h2 class="qaf-story__title" data-qaf-anim="words"><?php echo qaf_lines( $a['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
		<?php endif; ?>
		<?php if ( $a['text1'] ) : ?>
			<p class="qaf-story__text"<?php echo qaf_anim( 'up', 160 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo esc_html( $a['text1'] ); ?></p>
		<?php endif; ?>
		<?php if ( $a['text2'] ) : ?>
			<p class="qaf-story__text qaf-story__text--muted"<?php echo qaf_anim( 'up', 240 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo esc_html( $a['text2'] ); ?></p>
		<?php endif; ?>
	</div>
</section>
