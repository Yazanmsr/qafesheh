<?php
/**
 * Captcha — wires the portable QB Captcha module into Theme Settings.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

require_once QAF_DIR . 'inc/qb-captcha/qb-captcha.php';

add_filter(
	'qb_captcha_config',
	static function ( $cfg ) {
		return array_merge(
			$cfg,
			array(
				'provider'   => (string) qaf_opt( 'captcha_provider', 'shield' ),
				'mode'       => (string) qaf_opt( 'captcha_mode', 'invisible' ),
				'site_key'   => trim( (string) qaf_opt( 'captcha_site_key', '' ) ),
				'secret_key' => trim( (string) qaf_opt( 'captcha_secret_key', '' ) ),
				'strength'   => (string) qaf_opt( 'captcha_strength', 'balanced' ),
				'login'      => (bool) qaf_opt( 'captcha_login', 1 ),
				'comments'   => (bool) qaf_opt( 'captcha_comments', 1 ),
				'brand'      => 'QB Shield',
			)
		);
	}
);

/* Shown with the other security rows (Launch centre / Security screen). */
add_filter(
	'qaf_security_checks',
	static function ( $rows ) {
		$s      = qb_captcha_status();
		$rows[] = array(
			'label'  => __( 'الكابتشا على كل النماذج', 'qafesheh' ),
			'state'  => $s['state'],
			'detail' => $s['label'],
		);
		return $rows;
	}
);
