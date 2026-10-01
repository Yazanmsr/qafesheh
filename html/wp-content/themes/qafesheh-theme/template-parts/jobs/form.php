<?php
/**
 * Application form (job or open). Works without JS (admin-post) and upgrades to AJAX.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$qaf_job     = absint( $args['job_id'] ?? 0 );
$qaf_variant = ( $args['variant'] ?? 'light' ) === 'dark' ? 'dark' : 'light';
$qaf_uid     = 'qaf-apply-' . ( $qaf_job ? $qaf_job : 'open' );
$qaf_max     = max( 1, min( 10, absint( qaf_opt( 'apply_max_mb' ) ) ) );
$qaf_depts   = $qaf_job ? array() : get_terms( array( 'taxonomy' => 'qaf_job_dept', 'hide_empty' => false ) );
// phpcs:disable WordPress.Security.NonceVerification.Recommended
$qaf_done  = isset( $_GET['qaf-applied'] );
$qaf_error = isset( $_GET['qaf-apply-error'] );
// phpcs:enable
?>
<form class="qaf-apply qaf-apply--<?php echo esc_attr( $qaf_variant ); ?>" id="<?php echo esc_attr( $qaf_uid ); ?>" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data" novalidate data-qaf-apply data-max="<?php echo esc_attr( (string) ( $qaf_max * MB_IN_BYTES ) ); ?>" data-required="<?php echo esc_attr( qaf_opt_i18n( 'str_f_required' ) ); ?>" data-bad-email="<?php echo esc_attr( qaf_opt_i18n( 'str_f_bad_email' ) ); ?>" data-bad-file="<?php echo esc_attr( str_replace( '%s', (string) $qaf_max, (string) qaf_opt_i18n( 'str_f_bad_file' ) ) ); ?>" data-sending="<?php echo esc_attr( qaf_opt_i18n( 'str_f_sending' ) ); ?>" data-error="<?php echo esc_attr( qaf_opt_i18n( 'str_f_error' ) ); ?>">
	<input type="hidden" name="action" value="qaf_apply">
	<input type="hidden" name="qaf_job" value="<?php echo esc_attr( (string) $qaf_job ); ?>">
	<input type="hidden" name="qaf_t" value="<?php echo esc_attr( (string) time() ); ?>">
	<?php wp_nonce_field( 'qaf_apply', 'qaf_apply_nonce' ); ?>
	<div class="qaf-apply__hp" aria-hidden="true"><label>Website<input type="text" name="qaf_website" tabindex="-1" autocomplete="off"></label></div>

	<div class="qaf-apply__status" role="status" aria-live="polite"<?php echo ( $qaf_done || $qaf_error ) ? '' : ' hidden'; ?>>
		<?php
		if ( $qaf_done ) {
			echo qaf_text( 'apply_success' ); // phpcs:ignore WordPress.Security.EscapeOutput
		} elseif ( $qaf_error ) {
			echo qaf_text( 'str_f_error' ); // phpcs:ignore WordPress.Security.EscapeOutput
		}
		?>
	</div>

	<div class="qaf-apply__row">
		<?php qaf_apply_field( $qaf_uid, 'qaf_name', 'text', 'str_f_name', array( 'autocomplete' => 'name', 'required' => 'required', 'maxlength' => '120' ) ); ?>
		<?php qaf_apply_field( $qaf_uid, 'qaf_phone', 'tel', 'str_f_phone', array( 'autocomplete' => 'tel', 'required' => 'required', 'dir' => 'ltr', 'inputmode' => 'tel' ) ); ?>
	</div>
	<div class="qaf-apply__row<?php echo $qaf_depts && ! is_wp_error( $qaf_depts ) ? '' : ' qaf-apply__row--one'; ?>">
		<?php qaf_apply_field( $qaf_uid, 'qaf_email', 'email', 'str_f_email', array( 'autocomplete' => 'email', 'required' => 'required', 'dir' => 'ltr' ) ); ?>
		<?php if ( $qaf_depts && ! is_wp_error( $qaf_depts ) ) : ?>
			<label class="qaf-field qaf-field--select" for="<?php echo esc_attr( $qaf_uid . '-dept' ); ?>">
				<span class="screen-reader-text"><?php echo qaf_text( 'str_f_dept' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<select id="<?php echo esc_attr( $qaf_uid . '-dept' ); ?>" name="qaf_dept">
					<option value=""><?php echo qaf_text( 'str_f_dept' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></option>
					<?php foreach ( $qaf_depts as $qaf_d ) : ?>
						<option value="<?php echo esc_attr( (string) $qaf_d->term_id ); ?>"><?php echo esc_html( $qaf_d->name ); ?></option>
					<?php endforeach; ?>
				</select>
			</label>
		<?php endif; ?>
	</div>
	<label class="qaf-field qaf-field--area" for="<?php echo esc_attr( $qaf_uid . '-message' ); ?>">
		<span class="qaf-field__label"><?php echo qaf_text( $qaf_job ? 'str_f_msg_job' : 'str_f_msg_open' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		<textarea id="<?php echo esc_attr( $qaf_uid . '-message' ); ?>" name="qaf_message" rows="4" maxlength="3000" placeholder=" "></textarea>
	</label>

	<label class="qaf-file" for="<?php echo esc_attr( $qaf_uid . '-cv' ); ?>" data-name="qaf_cv">
		<input id="<?php echo esc_attr( $qaf_uid . '-cv' ); ?>" type="file" name="qaf_cv" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" required>
		<span class="qaf-file__icon"><?php echo qaf_icon( 'upload', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		<span class="qaf-file__text">
			<span class="qaf-file__title"><?php echo qaf_text( 'str_f_cv' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			<span class="qaf-file__hint" data-default="<?php echo esc_attr( str_replace( '%s', (string) $qaf_max, (string) qaf_opt_i18n( 'str_f_cv_hint' ) ) ); ?>"><?php echo esc_html( str_replace( '%s', (string) $qaf_max, (string) qaf_opt_i18n( 'str_f_cv_hint' ) ) ); ?></span>
		</span>
		<span class="qaf-file__btn"><?php echo qaf_text( 'str_f_choose' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		<span class="qaf-field__error" data-for="qaf_cv"></span>
	</label>

	<label class="qaf-check" data-name="qaf_consent">
		<input type="checkbox" name="qaf_consent" value="1" required>
		<span class="qaf-check__box" aria-hidden="true"><?php echo qaf_icon( 'check', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		<span><?php echo qaf_text( 'str_f_consent' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		<span class="qaf-field__error" data-for="qaf_consent"></span>
	</label>

	<div class="qaf-apply__foot">
		<span class="qaf-apply__note"><?php echo qaf_text( 'str_f_note' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		<?php if ( function_exists( 'qb_captcha_field' ) ) { qb_captcha_field( 'apply', array( 'class' => 'qaf-apply__captcha' ) ); } ?>
		<button class="qaf-btn qaf-btn--solid qaf-btn--lg qaf-apply__submit" type="submit"><span class="qaf-btn__label"><?php echo qaf_text( 'str_f_submit' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span><span class="qaf-apply__spin" aria-hidden="true"></span></button>
	</div>
</form>
