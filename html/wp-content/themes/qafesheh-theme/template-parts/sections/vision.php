<?php
/**
 * Vision / mission.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$a = wp_parse_args( $args ?? array(), qaf_seed_page_copy( 'about' )['vision'] + array( 'id' => '' ) );
?>
<section class="qaf-vision"<?php echo $a['id'] ? ' id="' . esc_attr( $a['id'] ) . '"' : ''; ?>>
	<div class="qaf-vision__grid" data-qaf-stagger>
		<?php foreach ( array( array( 'v', 'globe' ), array( 'm', 'spark' ) ) as $qaf_col ) : ?>
			<?php if ( ! empty( $a[ $qaf_col[0] . '_title' ] ) ) : ?>
				<div class="qaf-vision__col"<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
					<?php echo qaf_icon( $qaf_col[1], 34, 'qaf-vision__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<h2 class="qaf-vision__title"><?php echo esc_html( $a[ $qaf_col[0] . '_title' ] ); ?></h2>
					<p class="qaf-vision__text"><?php echo esc_html( $a[ $qaf_col[0] . '_text' ] ?? '' ); ?></p>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
</section>
