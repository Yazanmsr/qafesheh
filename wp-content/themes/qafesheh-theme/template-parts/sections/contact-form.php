<?php
/**
 * Contact: form | brands card, complaints promo, map.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$a        = wp_parse_args( $args ?? array(), qaf_seed_page_copy( 'contact' )['form'] + array( 'id' => '', 'brands' => true, 'promo' => true, 'map' => true ) );
$uid      = 'qaf-contact';
$companies = get_posts( array( 'post_type' => 'qaf_company', 'posts_per_page' => 6, 'orderby' => array( 'menu_order' => 'ASC' ), 'suppress_filters' => false ) );
$complaints = get_posts( array( 'post_type' => 'page', 'meta_key' => '_qaf_import_key', 'meta_value' => 'page-complaints', 'posts_per_page' => 1, 'suppress_filters' => false ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
$map_q    = trim( (string) qaf_opt( 'fc_map_query' ) );
?>
<section class="qaf-sec qaf-formsec"<?php echo $a['id'] ? ' id="' . esc_attr( $a['id'] ) . '"' : ''; ?>>
	<div class="qaf-container qaf-formsec__grid">
		<div class="qaf-formsec__card"<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
			<?php if ( $a['title'] ) : ?>
				<h2 class="qaf-formsec__title"><?php echo esc_html( $a['title'] ); ?></h2>
			<?php endif; ?>
			<?php qaf_form_open( 'contact' ); ?>
				<div class="qaf-apply__row">
					<?php qaf_apply_field( $uid, 'qaf_name', 'text', 'str_f_name', array( 'autocomplete' => 'name', 'required' => 'required', 'maxlength' => '120' ) ); ?>
					<?php qaf_apply_field( $uid, 'qaf_phone', 'tel', 'str_f_phone', array( 'autocomplete' => 'tel', 'required' => 'required', 'dir' => 'ltr', 'inputmode' => 'tel' ) ); ?>
				</div>
				<div class="qaf-apply__row">
					<?php qaf_apply_field( $uid, 'qaf_email', 'email', 'str_f_email', array( 'autocomplete' => 'email', 'required' => 'required', 'dir' => 'ltr' ) ); ?>
					<?php qaf_form_select( 'qaf_company', 'str_f_target', qaf_form_options( 'companies_group' ) ); ?>
				</div>
				<?php qaf_apply_field( $uid, 'qaf_subject', 'text', 'str_f_subject', array( 'required' => 'required', 'maxlength' => '190' ) ); ?>
				<?php qaf_form_textarea( 'qaf_message', 'str_f_message', 7 ); ?>
			<?php qaf_form_close( 'str_reply_note' ); ?>
		</div>

		<aside class="qaf-formsec__side">
			<?php if ( $a['brands'] && $companies ) : ?>
				<div class="qaf-side qaf-side--ink qaf-bcontacts"<?php echo qaf_anim( 'up', 80 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
					<p class="qaf-side__label"><?php echo qaf_text( 'fc_brands_label' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
					<ul>
						<?php foreach ( $companies as $p ) : ?>
							<li style="--qaf-accent:<?php echo esc_attr( qaf_company_color( $p->ID ) ); ?>">
								<a class="qaf-bcontacts__name" href="<?php echo esc_url( get_permalink( $p ) ); ?>"><?php echo esc_html( get_the_title( $p ) ); ?></a>
								<?php if ( qaf_meta( 'email', $p->ID ) ) : ?>
									<a class="qaf-bcontacts__mail" href="mailto:<?php echo esc_attr( antispambot( qaf_meta( 'email', $p->ID ) ) ); ?>" data-qaf-nofx><?php echo esc_html( antispambot( qaf_meta( 'email', $p->ID ) ) ); ?></a>
								<?php endif; ?>
								<?php if ( qaf_meta( 'phone', $p->ID ) ) : ?>
									<a class="qaf-bcontacts__phone" href="<?php echo esc_attr( qaf_tel( qaf_meta( 'phone', $p->ID ) ) ); ?>" dir="ltr" data-qaf-nofx><?php echo esc_html( qaf_meta( 'phone', $p->ID ) ); ?></a>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<?php if ( $a['promo'] && $complaints ) : ?>
				<a class="qaf-side qaf-side--soft qaf-promo" href="<?php echo esc_url( get_permalink( $complaints[0] ) ); ?>"<?php echo qaf_anim( 'up', 160 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
					<span class="qaf-side__h"><?php echo qaf_text( 'fc_promo_title' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<span class="qaf-promo__text"><?php echo qaf_text( 'fc_promo_text' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<span class="qaf-promo__link"><span><?php echo qaf_text( 'fc_promo_link' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span><?php echo qaf_icon( 'arrow', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				</a>
			<?php endif; ?>

			<?php if ( $a['map'] && $map_q ) : ?>
				<div class="qaf-formsec__map"<?php echo qaf_anim( 'fade', 220 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
					<iframe class="qaf-branches__frame" title="map" data-src="<?php echo esc_url( 'https://maps.google.com/maps?q=' . rawurlencode( $map_q ) . '&z=14&output=embed&hl=' . rawurlencode( qaf_current_lang() ) ); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" data-qaf-lazymap></iframe>
				</div>
			<?php endif; ?>
		</aside>
	</div>
</section>
