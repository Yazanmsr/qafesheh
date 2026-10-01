<?php
/**
 * Contact channels: email, phone, head office (values from Theme Options).
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$a     = wp_parse_args( $args ?? array(), qaf_seed_page_copy( 'contact' )['channels'] + array( 'id' => '' ) );
$items = array_filter(
	array(
		array( 'mail', $a['email_title'], qaf_opt( 'c_email' ), 'mailto:' . antispambot( (string) qaf_opt( 'c_email' ) ), '' ),
		array( 'phone', $a['phone_title'], qaf_opt( 'c_phone' ), qaf_tel( qaf_opt( 'c_phone' ) ), 'ltr' ),
		array( 'pin', $a['address_title'], qaf_opt_i18n( 'c_address' ), '', '' ),
	),
	static fn( $i ) => '' !== (string) $i[2]
);
?>
<section class="qaf-channels"<?php echo $a['id'] ? ' id="' . esc_attr( $a['id'] ) . '"' : ''; ?>>
	<div class="qaf-channels__grid" data-qaf-stagger>
		<?php foreach ( $items as $item ) : ?>
			<?php $tag = $item[3] ? 'a' : 'div'; ?>
			<<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput ?> class="qaf-channels__item"<?php echo $item[3] ? ' href="' . esc_attr( $item[3] ) . '" data-qaf-nofx' : ''; ?><?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
				<?php echo qaf_icon( $item[0], 26, 'qaf-channels__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<span class="qaf-channels__title"><?php echo esc_html( $item[1] ); ?></span>
				<span class="qaf-channels__value"<?php echo $item[4] ? ' dir="ltr"' : ''; ?>><?php echo esc_html( 'mail' === $item[0] ? antispambot( (string) $item[2] ) : (string) $item[2] ); ?></span>
			</<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
		<?php endforeach; ?>
	</div>
</section>
