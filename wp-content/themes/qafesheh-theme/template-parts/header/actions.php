<?php
/**
 * Header actions: CTA + burger.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="qaf-mast__actions">
	<?php qaf_button( qaf_opt_i18n( 'header_cta_text' ), qaf_opt_i18n( 'header_cta_url' ), 'solid', 'qaf-mast__cta' ); ?>
	<button class="qaf-burger" type="button" aria-expanded="false" aria-controls="qaf-mobile" aria-label="<?php echo esc_attr( qaf_opt_i18n( 'str_menu' ) ); ?>">
		<span class="qaf-burger__lines" aria-hidden="true"><i></i><i></i><i></i></span>
	</button>
</div>
