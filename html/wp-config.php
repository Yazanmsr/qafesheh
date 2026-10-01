<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'qafesheh_group_qbdb' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'E<_Q)uBJhu&|i.,yld`>}0$3grjp$p>y 02,>[aO)kyF5li{TzV,?4f;jKD6i;^]' );
define( 'SECURE_AUTH_KEY',  ':+F2BGt:<K6|tA;vy+6M@ESd-=N/h4G$ti&N>hm9K8!S,$>PdpI:4ga/RtMm(ayy' );
define( 'LOGGED_IN_KEY',    'x&VoPyEh,EfrNlO$VzC1N{GN?2`NaB!r^ P>ko@/-OsYA^v^g;[,Doa{qEmu#a<|' );
define( 'NONCE_KEY',        'n(#;dIb1}/P0?!%yvN(Y:i^IELfI?A$9ENyRF`u$:Gsbz8iw>rPAiplW=EWyrQ4y' );
define( 'AUTH_SALT',        'FVQKyodDsq%K9q/qqwCz]:~boVu8equGFE?0Fl}oSJ(L4]r^!YDzkWD)t-o&j.X?' );
define( 'SECURE_AUTH_SALT', '9I1&> Fda_B,?roH,-H?N2Hx|%|8c=Qw!bl3DsS3*DH`FhSa-_.uO{O4Ofa[ }Sw' );
define( 'LOGGED_IN_SALT',   '~})p8+% 8;}<9..L/]f[vW+~m{o7v;bah953OYuE;1U}}4MKe?7=6L;;*k1IZ5}]' );
define( 'NONCE_SALT',       'e08 AI `snC%PQ8,:xXJK32u 3I3)IB]S$p,,gdWOJs}1D4A$W[(Ir.G:LFY%aOk' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'qf7qb_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
