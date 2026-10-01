<?php
/**
 * Qafesheh Group — bootstrap only.
 *
 * Constants + ordered include list. No features, snippets or markup here.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

define( 'QAF_VERSION', '3.1.0' );
define( 'QAF_DIR', trailingslashit( get_template_directory() ) );
define( 'QAF_URI', trailingslashit( get_template_directory_uri() ) );

/**
 * Require a theme include if it exists.
 *
 * @param string[] $files Relative paths.
 */
function qaf_require( array $files ) {
	foreach ( $files as $file ) {
		$path = QAF_DIR . ltrim( $file, '/' );
		if ( file_exists( $path ) ) {
			require_once $path;
		}
	}
}

/* Core */
qaf_require(
	array(
		'inc/options-schema.php',
		'inc/options.php',
		'inc/setup.php',
		'inc/svg.php',
		'inc/dynamic-css.php',
		'inc/qaf-fonts.php',
		'inc/enqueue.php',
		'inc/template-tags.php',
		'inc/breadcrumbs.php',
		'inc/post-types.php',
		'inc/metaboxes.php',
		'inc/wpml.php',
		'inc/ajax.php',
		'inc/applications.php',
		'inc/forms.php',
		'inc/blog.php',
		'inc/data/seed.php',
		'inc/emails.php',
		'inc/security.php',
		'inc/security-hardening.php',
		'inc/security-2fa.php',
		'inc/captcha.php',
		'inc/login.php',
		'inc/offers.php',
		'inc/admin/brand.php',
	)
);

/* Admin */
if ( is_admin() ) {
	qaf_require(
		array(
			'inc/admin/options-page.php',
			'inc/admin/importer.php',
			'inc/admin/applications-admin.php',
			'inc/admin/entries-admin.php',
			'inc/admin/launch.php',
		)
	);
}

/* Elementor */
qaf_require( array( 'inc/elementor/elementor-init.php' ) );
