<?php
/**
 * Top utility bar.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

$qaf_address = qaf_opt_i18n( 'top_address' );
$qaf_phone   = qaf_opt( 'top_phone' );
$qaf_util    = qaf_menu( 'utility', 'qaf-topbar__menu' );
?>
<div class="qaf-topbar">
	<div class="qaf-container qaf-topbar__in">
		<div class="qaf-topbar__info">
			<?php if ( $qaf_address ) : ?>
				<span class="qaf-topbar__address"><?php echo esc_html( $qaf_address ); ?></span>
			<?php endif; ?>
			<?php if ( $qaf_address && $qaf_phone ) : ?>
				<span class="qaf-topbar__sep qaf-topbar__sep--gold" aria-hidden="true">|</span>
			<?php endif; ?>
			<?php if ( $qaf_phone ) : ?>
				<a class="qaf-topbar__phone" href="<?php echo esc_attr( qaf_tel( $qaf_phone ) ); ?>" dir="ltr" data-qaf-nofx><?php echo esc_html( $qaf_phone ); ?></a>
			<?php endif; ?>
		</div>
		<div class="qaf-topbar__links">
			<?php echo $qaf_util; // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php qaf_lang_switcher( 'qaf-lang qaf-topbar__lang' ); ?>
		</div>
	</div>
</div>
