<?php
/**
 * Complaints: form (type chips, company → branch, attachment) | pledge, direct contact, status lookup.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$a        = wp_parse_args( $args ?? array(), qaf_seed_page_copy( 'complaints' )['form'] + array( 'id' => '', 'pledge' => true, 'direct' => true, 'track' => true ) );
$uid      = 'qaf-complaint';
$kinds    = qaf_forms()['complaint']['fields']['kind']['options'];
$branches = qaf_form_options( 'branches' );
?>
<section class="qaf-sec qaf-formsec"<?php echo $a['id'] ? ' id="' . esc_attr( $a['id'] ) . '"' : ''; ?>>
	<div class="qaf-container qaf-formsec__grid">
		<div class="qaf-formsec__card"<?php echo qaf_anim( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
			<?php if ( $a['title'] ) : ?>
				<h2 class="qaf-formsec__title"><?php echo esc_html( $a['title'] ); ?></h2>
			<?php endif; ?>
			<?php qaf_form_open( 'complaint' ); ?>
				<fieldset class="qaf-kinds">
					<legend class="qaf-side__label"><?php echo qaf_text( 'str_f_kind' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></legend>
					<div class="qaf-kinds__opts">
						<?php $first = true; ?>
						<?php foreach ( $kinds as $value => $label ) : ?>
							<label class="qaf-kind"><input type="radio" name="qaf_kind" value="<?php echo esc_attr( $value ); ?>"<?php checked( $first ); ?>><span><?php echo qaf_text( $label ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span></label>
							<?php $first = false; ?>
						<?php endforeach; ?>
					</div>
					<span class="qaf-field__error" data-for="qaf_kind"></span>
				</fieldset>
				<div class="qaf-apply__row">
					<?php qaf_apply_field( $uid, 'qaf_name', 'text', 'str_f_name', array( 'autocomplete' => 'name', 'required' => 'required', 'maxlength' => '120' ) ); ?>
					<?php qaf_apply_field( $uid, 'qaf_phone', 'tel', 'str_f_phone', array( 'autocomplete' => 'tel', 'required' => 'required', 'dir' => 'ltr', 'inputmode' => 'tel' ) ); ?>
				</div>
				<div class="qaf-apply__row">
					<?php qaf_apply_field( $uid, 'qaf_email', 'email', 'str_f_email', array( 'autocomplete' => 'email', 'required' => 'required', 'dir' => 'ltr' ) ); ?>
					<?php qaf_form_select( 'qaf_company', 'str_f_company', qaf_form_options( 'companies' ), 'brand' ); ?>
				</div>
				<div class="qaf-apply__row">
					<?php qaf_form_select( 'qaf_branch', 'str_f_branch', $branches, 'brands' ); ?>
					<?php qaf_apply_field( $uid, 'qaf_order', 'text', 'str_f_order', array( 'maxlength' => '60', 'dir' => 'auto' ) ); ?>
				</div>
				<?php qaf_apply_field( $uid, 'qaf_subject', 'text', 'str_f_subject', array( 'required' => 'required', 'maxlength' => '190' ) ); ?>
				<?php qaf_form_textarea( 'qaf_message', 'str_f_details', 6 ); ?>
				<label class="qaf-file" for="<?php echo esc_attr( $uid . '-file' ); ?>">
					<input id="<?php echo esc_attr( $uid . '-file' ); ?>" type="file" name="qaf_file" accept=".jpg,.jpeg,.png,.webp,.pdf,image/jpeg,image/png,image/webp,application/pdf">
					<span class="qaf-file__icon"><?php echo qaf_icon( 'upload', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<span class="qaf-file__text">
						<span class="qaf-file__title"><?php echo qaf_text( 'str_f_attach' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<span class="qaf-file__hint" data-default="<?php echo esc_attr( qaf_opt_i18n( 'str_f_attach_hint' ) ); ?>"><?php echo qaf_text( 'str_f_attach_hint' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					</span>
					<span class="qaf-file__btn"><?php echo qaf_text( 'str_f_choose' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<span class="qaf-field__error" data-for="qaf_file"></span>
				</label>
			<?php qaf_form_close( 'str_track_note' ); ?>
		</div>

		<aside class="qaf-formsec__side">
			<?php if ( $a['pledge'] ) : ?>
				<div class="qaf-side qaf-side--ink qaf-pledge"<?php echo qaf_anim( 'up', 80 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
					<?php echo qaf_icon( 'shield', 30, 'qaf-pledge__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<h2 class="qaf-pledge__title"><?php echo qaf_text( 'fp_pledge_title' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
					<p class="qaf-pledge__text"><?php echo qaf_text( 'fp_pledge_text' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
				</div>
			<?php endif; ?>

			<?php if ( $a['direct'] && ( qaf_opt( 'c_email' ) || qaf_opt( 'c_phone' ) ) ) : ?>
				<div class="qaf-side qaf-side--line qaf-direct"<?php echo qaf_anim( 'up', 160 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
					<h2 class="qaf-side__h"><?php echo qaf_text( 'fp_direct_title' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
					<?php if ( qaf_opt( 'c_email' ) ) : ?>
						<a class="qaf-direct__row" href="mailto:<?php echo esc_attr( antispambot( qaf_opt( 'c_email' ) ) ); ?>" data-qaf-nofx><?php echo qaf_icon( 'mail', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( antispambot( qaf_opt( 'c_email' ) ) ); ?></span></a>
					<?php endif; ?>
					<?php if ( qaf_opt( 'c_phone' ) ) : ?>
						<a class="qaf-direct__row" href="<?php echo esc_attr( qaf_tel( qaf_opt( 'c_phone' ) ) ); ?>" data-qaf-nofx><?php echo qaf_icon( 'phone', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span dir="ltr"><?php echo esc_html( qaf_opt( 'c_phone' ) ); ?></span></a>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( $a['track'] ) : ?>
				<form class="qaf-side qaf-side--soft qaf-track" data-qaf-track data-nonce="<?php echo esc_attr( wp_create_nonce( 'qaf_track' ) ); ?>" data-none="<?php echo esc_attr( qaf_opt_i18n( 'str_track_none' ) ); ?>" novalidate<?php echo qaf_anim( 'up', 240 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
					<h2 class="qaf-side__h"><?php echo qaf_text( 'fp_track_title' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
					<p class="qaf-promo__text"><?php echo qaf_text( 'fp_track_text' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
					<?php qaf_apply_field( 'qaf-track', 'tracking', 'text', 'str_track_number', array( 'dir' => 'ltr', 'autocomplete' => 'off', 'maxlength' => '20' ) ); ?>
					<?php qaf_apply_field( 'qaf-track', 'email', 'email', 'str_f_email', array( 'dir' => 'ltr', 'autocomplete' => 'email' ) ); ?>
					<button class="qaf-btn qaf-btn--ink qaf-track__btn" type="submit"><span class="qaf-btn__label"><?php echo qaf_text( 'str_track_btn' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span></button>
					<p class="qaf-track__result" role="status" aria-live="polite" hidden></p>
				</form>
			<?php endif; ?>
		</aside>
	</div>
</section>
