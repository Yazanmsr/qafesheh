<?php
/**
 * Service sidebar: request card, other services, direct contact.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$qaf_form   = trim( (string) qaf_opt( 'svc_form' ) );
$qaf_others = get_posts(
	array(
		'post_type'        => 'qaf_service',
		'posts_per_page'   => 6,
		'post__not_in'     => array( get_the_ID() ),
		'orderby'          => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
		'suppress_filters' => false,
	)
);
$qaf_contact = add_query_arg( 'service', rawurlencode( get_the_title() ), qaf_resolve_url( (string) qaf_opt_i18n( 'svc_cta_url' ) ) );
?>
<div class="qaf-side qaf-side--ink"<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<p class="qaf-side__label"><?php echo qaf_text( 'str_request' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
	<h2 class="qaf-side__title"><?php echo qaf_text( 'str_request_t' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
	<?php if ( $qaf_form && 0 === strpos( $qaf_form, '[contact-form-7' ) && shortcode_exists( 'contact-form-7' ) ) : ?>
		<div class="qaf-side__form qaf-form qaf-form--dark"><?php echo do_shortcode( $qaf_form ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
	<?php else : ?>
		<?php qaf_button( qaf_opt_i18n( 'str_request_btn' ), $qaf_contact, 'solid', 'qaf-side__btn' ); ?>
	<?php endif; ?>
</div>

<?php if ( $qaf_others ) : ?>
	<div class="qaf-side qaf-side--soft"<?php echo qaf_anim( 'up', 100 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
		<p class="qaf-side__label"><?php echo qaf_text( 'str_other_svc' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
		<ul class="qaf-side__list">
			<?php foreach ( $qaf_others as $qaf_p ) : ?>
				<li><a href="<?php echo esc_url( get_permalink( $qaf_p ) ); ?>"><span><?php echo esc_html( get_the_title( $qaf_p ) ); ?></span><?php echo qaf_icon( 'arrow', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a></li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>

<?php if ( qaf_opt( 'c_phone' ) || qaf_opt( 'c_email' ) ) : ?>
	<div class="qaf-side qaf-side--line"<?php echo qaf_anim( 'up', 180 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
		<h2 class="qaf-side__h"><?php echo qaf_text( 'str_direct' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
		<?php if ( qaf_opt( 'c_phone' ) ) : ?>
			<a href="<?php echo esc_attr( qaf_tel( qaf_opt( 'c_phone' ) ) ); ?>" dir="ltr" data-qaf-nofx><?php echo esc_html( qaf_opt( 'c_phone' ) ); ?></a>
		<?php endif; ?>
		<?php if ( qaf_opt( 'c_email' ) ) : ?>
			<a href="mailto:<?php echo esc_attr( antispambot( qaf_opt( 'c_email' ) ) ); ?>" data-qaf-nofx><?php echo esc_html( antispambot( qaf_opt( 'c_email' ) ) ); ?></a>
		<?php endif; ?>
	</div>
<?php endif; ?>
