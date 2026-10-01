<?php
/**
 * Emails in the Qafesheh Group identity — every email the site sends.
 *
 * · One layout (warm stone canvas, paper card, logo, diamond rule, gold accents, dark footer with
 *   WhatsApp / phone / email) used by every email the site sends: contact, complaints,
 *   job applications and their auto-replies, and WordPress's own mail.
 * · WordPress core + plugin plain-text emails are wrapped in the same layout automatically,
 *   so nothing ever reaches a person in default WordPress styling.
 *
 * Email clients ignore web fonts, <style> in <head> is stripped by some, so the shell is inline-styled.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

/**
 * Brand values for emails.
 *
 * @return array
 */
function qaf_email_brand() {
	/* The seal reads on both the dark email footer and the paper card. SVG never renders in mail. */
	$logo = (string) qaf_opt( 'logo' );
	$logo = $logo && preg_match( '/\.(png|jpe?g|gif)$/i', $logo ) ? $logo : 'assets/images/logos/logo-qafesheh-seal.png';
	return array(
		'rtl'     => is_rtl(),
		'en'      => 0 === strpos( determine_locale(), 'en' ),
		'logo'    => 0 === strpos( $logo, 'http' ) ? $logo : QAF_URI . ltrim( $logo, '/' ),
		'band'    => '',
		'name'    => (string) ( qaf_opt_i18n( 'brand_name' ) ? qaf_opt_i18n( 'brand_name' ) : get_bloginfo( 'name' ) ),
		'tag'     => 0 === strpos( determine_locale(), 'en' ) ? 'From Jordan to the Gulf and the Arab world' : 'من الأردن إلى الخليج والوطن العربي',
		'phone'   => (string) qaf_opt( 'c_phone' ),
		'email'   => (string) qaf_opt( 'c_email' ),
		'wa'      => qaf_opt( 's_whatsapp' ) ? 'https://wa.me/' . preg_replace( '/\D/', '', (string) qaf_opt( 's_whatsapp' ) ) : '',
		'site'    => home_url( '/' ),
		'crimson' => (string) ( qaf_opt( 'c_primary' ) ? qaf_opt( 'c_primary' ) : '#B8912F' ),
		'ink'     => (string) ( qaf_opt( 'c_ink' ) ? qaf_opt( 'c_ink' ) : '#0F0E0C' ),
		'ivory'   => (string) ( qaf_opt( 'c_bg' ) ? qaf_opt( 'c_bg' ) : '#F2EEE7' ),
		'paper'   => '#FFFCF6',
		'body'    => '#4A4340',
		'line'    => (string) ( qaf_opt( 'c_line' ) ? qaf_opt( 'c_line' ) : '#D6CDBD' ),
		'font'    => "'IBM Plex Sans Arabic','Segoe UI',Tahoma,Arial,sans-serif",
	);
}

/**
 * Opening of the shell (up to where the content goes).
 *
 * @param string $heading Heading.
 * @param string $kicker  Small line above the heading.
 * @return string
 */
function qaf_email_open( $heading, $kicker = '' ) {
	$b     = qaf_email_brand();
	$dir   = $b['rtl'] ? 'rtl' : 'ltr';
	$align = $b['rtl'] ? 'right' : 'left';
	$kicker = $kicker ? $kicker : $b['name'];
	ob_start();
	?>
<div dir="<?php echo esc_attr( $dir ); ?>" style="margin:0;padding:0;background:<?php echo esc_attr( $b['ivory'] ); ?>;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:<?php echo esc_attr( $b['ivory'] ); ?>;font-family:<?php echo esc_attr( $b['font'] ); ?>;">
<tr><td align="center" style="padding:32px 12px 40px;">
	<table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:600px;">
		<tr><td align="center" style="padding:0 0 22px;">
			<a href="<?php echo esc_url( $b['site'] ); ?>" target="_blank" style="text-decoration:none;"><img src="<?php echo esc_url( $b['logo'] ); ?>" width="150" alt="<?php echo esc_attr( $b['name'] ); ?>" style="display:block;width:150px;max-width:150px;height:auto;border:0;"></a>
		</td></tr>
		<tr><td style="background:<?php echo esc_attr( $b['paper'] ); ?>;border-radius:24px;overflow:hidden;border:1px solid <?php echo esc_attr( $b['line'] ); ?>;">
			<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr><td style="height:6px;line-height:6px;font-size:0;background:<?php echo esc_attr( $b['crimson'] ); ?>;">&nbsp;</td></tr>
				<tr><td dir="<?php echo esc_attr( $dir ); ?>" style="padding:34px 40px 8px;text-align:<?php echo esc_attr( $align ); ?>;">
					<p style="margin:0 0 10px;font-size:13px;line-height:1.6;color:<?php echo esc_attr( $b['crimson'] ); ?>;font-weight:600;letter-spacing:.02em;">&#9670;&#9670;&#9670;&nbsp;&nbsp;<?php echo esc_html( $kicker ); ?></p>
					<h1 style="margin:0;font-size:28px;line-height:1.35;color:<?php echo esc_attr( $b['ink'] ); ?>;font-weight:700;font-family:<?php echo esc_attr( $b['font'] ); ?>;"><?php echo esc_html( $heading ); ?></h1>
				</td></tr>
				<tr><td dir="<?php echo esc_attr( $dir ); ?>" class="qaf-email-body" style="padding:18px 40px 36px;text-align:<?php echo esc_attr( $align ); ?>;font-size:15px;line-height:1.9;color:<?php echo esc_attr( $b['body'] ); ?>;">
	<?php
	return ob_get_clean();
}

/**
 * Closing of the shell (footer).
 *
 * @return string
 */
function qaf_email_close() {
	$b   = qaf_email_brand();
	$dir = $b['rtl'] ? 'rtl' : 'ltr';
	ob_start();
	?>
				</td></tr>
			</table>
		</td></tr>
		<tr><td style="padding:18px 0 0;">
			<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:<?php echo esc_attr( $b['ink'] ); ?>;border-radius:24px;">
				<tr><td align="center" dir="<?php echo esc_attr( $dir ); ?>" style="padding:30px 28px 26px;color:#CFC4B6;font-size:13px;line-height:1.9;text-align:center;">
					<?php if ( $b['band'] ) : ?>
						<img src="<?php echo esc_url( $b['band'] ); ?>" width="220" alt="" style="display:block;margin:0 auto 16px;width:220px;max-width:70%;height:auto;border:0;opacity:.9;">
					<?php endif; ?>
					<p style="margin:0 0 16px;color:#F2E9DE;font-size:16px;font-weight:600;"><?php echo esc_html( $b['tag'] ); ?></p>
					<?php if ( $b['wa'] ) : ?>
						<a href="<?php echo esc_url( $b['wa'] ); ?>" target="_blank" style="display:inline-block;background:#1F8F4E;color:#ffffff;text-decoration:none;border-radius:999px;padding:11px 26px;font-size:14px;font-weight:600;"><?php echo esc_html( $b['en'] ? 'Talk to us on WhatsApp' : 'تواصل معنا على واتساب' ); ?></a>
					<?php endif; ?>
					<p style="margin:18px 0 0;">
						<?php if ( $b['phone'] ) : ?><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $b['phone'] ) ); ?>" style="color:#CFC4B6;text-decoration:none;" dir="ltr"><?php echo esc_html( $b['phone'] ); ?></a><?php endif; ?>
						<?php if ( $b['phone'] && $b['email'] ) : ?>&nbsp;&nbsp;<span style="color:#B8912F;">&#9670;</span>&nbsp;&nbsp;<?php endif; ?>
						<?php if ( $b['email'] ) : ?><a href="mailto:<?php echo esc_attr( $b['email'] ); ?>" style="color:#CFC4B6;text-decoration:none;"><?php echo esc_html( $b['email'] ); ?></a><?php endif; ?>
					</p>
					<p style="margin:6px 0 0;"><a href="<?php echo esc_url( $b['site'] ); ?>" style="color:#D9B85C;text-decoration:none;"><?php echo esc_html( wp_parse_url( $b['site'], PHP_URL_HOST ) ); ?></a></p>
				</td></tr>
			</table>
		</td></tr>
		<tr><td align="center" style="padding:16px 10px 0;font-size:11px;color:#8A7F76;font-family:<?php echo esc_attr( $b['font'] ); ?>;">© <?php echo esc_html( wp_date( 'Y' ) . ' ' . $b['name'] ); ?></td></tr>
	</table>
</td></tr>
</table>
</div>
	<?php
	return ob_get_clean();
}

/**
 * Full email document for non-WooCommerce emails.
 *
 * @param string $heading Heading.
 * @param string $html    Content (HTML).
 * @param string $kicker  Kicker.
 * @return string
 */
function qaf_email_document( $heading, $html, $kicker = '' ) {
	$b = qaf_email_brand();
	return '<!DOCTYPE html><html lang="' . esc_attr( $b['en'] ? 'en' : 'ar' ) . '" dir="' . ( $b['rtl'] ? 'rtl' : 'ltr' ) . '"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>' . esc_html( $heading ) . '</title></head><body style="margin:0;padding:0;background:' . esc_attr( $b['ivory'] ) . ';">'
		. qaf_email_open( $heading, $kicker ) . $html . qaf_email_close() . '</body></html>';
}

/**
 * A branded button for email content.
 *
 * @param string $label Label.
 * @param string $url   URL.
 * @return string
 */
function qaf_email_button( $label, $url ) {
	return '<p style="margin:24px 0 8px;"><a href="' . esc_url( $url ) . '" target="_blank" style="display:inline-block;background:#B8912F;color:#ffffff;text-decoration:none;border-radius:999px;padding:13px 30px;font-size:15px;font-weight:600;">' . esc_html( $label ) . '</a></p>';
}

/**
 * Key/value card (used by the form emails).
 *
 * @param array $rows label => value.
 * @return string
 */
function qaf_email_rows( $rows ) {
	$b   = qaf_email_brand();
	$out = '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:8px 0 18px;border:1px solid ' . esc_attr( $b['line'] ) . ';border-radius:16px;border-collapse:separate;overflow:hidden;">';
	$i   = 0;
	foreach ( $rows as $k => $v ) {
		if ( '' === (string) $v ) {
			continue;
		}
		$out .= '<tr><td style="padding:12px 18px;width:130px;background:#F6EEE3;color:#6B605A;font-size:13px;vertical-align:top;' . ( $i ? 'border-top:1px solid ' . esc_attr( $b['line'] ) . ';' : '' ) . '">' . esc_html( $k ) . '</td><td style="padding:12px 18px;color:#141414;font-size:14px;' . ( $i ? 'border-top:1px solid ' . esc_attr( $b['line'] ) . ';' : '' ) . '">' . nl2br( esc_html( (string) $v ) ) . '</td></tr>';
		++$i;
	}
	return $out . '</table>';
}

/* ================================================= WordPress core / plugin plain-text emails */

/**
 * Wrap plain-text emails in the branded layout (skips anything already HTML).
 *
 * @param array $args wp_mail args.
 * @return array
 */
function qaf_email_wrap_plain( $args ) {
	if ( ! apply_filters( 'qaf_brand_plain_emails', true ) || ! empty( $GLOBALS['qaf_email_raw'] ) ) {
		return $args;
	}
	$msg     = (string) ( $args['message'] ?? '' );
	$headers = is_array( $args['headers'] ?? '' ) ? implode( "\n", $args['headers'] ) : (string) ( $args['headers'] ?? '' );
	if ( '' === trim( $msg ) || preg_match( '/content-type:\s*(text\/html|multipart)/i', $headers ) || preg_match( '/<(html|body|table|div|p)[\s>]/i', $msg ) ) {
		return $args;
	}
	$subject = wp_specialchars_decode( (string) ( $args['subject'] ?? '' ), ENT_QUOTES );
	$heading = trim( preg_replace( '/^\[[^\]]*\]\s*/u', '', $subject ) );
	/* Drop a first line that only repeats the subject. */
	$parts = preg_split( '/\r?\n/', ltrim( $msg ), 2 );
	if ( $heading && isset( $parts[1] ) && '' !== trim( $parts[0] ) && false !== mb_strpos( $heading, trim( $parts[0] ) ) ) {
		$msg = $parts[1];
	}
	$args['message'] = qaf_email_document( $heading ? $heading : get_bloginfo( 'name' ), qaf_email_plain_to_html( $msg ) );
	/* Replace any plain-text content type the sender set, then declare HTML. */
	$lines = array_filter( preg_split( '/\r?\n/', $headers ), static fn( $h ) => '' !== trim( $h ) && ! preg_match( '/^content-type:/i', trim( $h ) ) );
	$lines[] = 'Content-Type: text/html; charset=UTF-8';
	$args['headers'] = $lines;
	return $args;
}

/**
 * Plain text → branded HTML.
 * «Label: value» runs become a rows card, a dashboard link becomes a button, a phone gets a
 * WhatsApp button, everything else stays a readable paragraph with clickable links.
 *
 * @param string $msg Plain text.
 * @return string
 */
function qaf_email_plain_to_html( $msg ) {
	$b      = qaf_email_brand();
	$msg    = preg_replace( '/<(https?:\/\/[^>\s]+)>/', '$1', (string) $msg );
	$blocks = array();
	$rows   = array();
	$phone  = '';
	$flush  = static function () use ( &$rows, &$blocks ) {
		if ( $rows ) {
			$blocks[] = qaf_email_rows( $rows );
			$rows     = array();
		}
	};
	foreach ( preg_split( '/\r?\n/', $msg ) as $line ) {
		$line = trim( $line );
		if ( '' === $line ) {
			$flush();
			continue;
		}
		/* A link to the dashboard (or any URL on its own labelled line) → button. */
		if ( preg_match( '/^([^:：]{1,60})[:：]\s*(https?:\/\/\S+)$/u', $line, $m ) && false !== strpos( $m[2], '/wp-admin/' ) ) {
			$flush();
			$blocks[] = qaf_email_button( 'فتح بلوحة التحكم', $m[2] );
			continue;
		}
		if ( preg_match( '/^([^:：]{1,40})[:：]\s+(.+)$/u', $line, $m ) && false === strpos( $m[1], 'http' ) ) {
			$label          = trim( $m[1] );
			$rows[ $label ] = trim( $m[2] );
			if ( ! $phone && preg_match( '/هاتف|جوال|موبايل|phone|mobile|whatsapp|واتساب/iu', $label ) ) {
				$phone = trim( $m[2] );
			}
			continue;
		}
		$flush();
		$html     = make_clickable( esc_html( $line ) );
		$html     = preg_replace( '/<a href=/', '<a style="color:' . esc_attr( $b['crimson'] ) . ';font-weight:600;word-break:break-all;" href=', $html );
		$blocks[] = '<p style="margin:0 0 12px;">' . $html . '</p>';
	}
	$flush();
	if ( $phone && function_exists( 'qaf_wa_number' ) && qaf_wa_number( $phone ) ) {
		$blocks[] = '<p style="margin:6px 0 0;"><a href="' . esc_url( 'https://wa.me/' . qaf_wa_number( $phone ) ) . '" target="_blank" style="display:inline-block;background:#1F8F4E;color:#ffffff;text-decoration:none;border-radius:999px;padding:11px 24px;font-size:14px;font-weight:600;">الرد على واتساب</a></p>';
	}
	return implode( '', $blocks );
}
add_filter( 'wp_mail', 'qaf_email_wrap_plain', 20 );


/**
 * A plain paragraph for branded email content.
 *
 * @param string $text Text.
 * @return string
 */
function qaf_email_p( $text ) {
	return '<p style="margin:0 0 14px;">' . esc_html( (string) $text ) . '</p>';
}
