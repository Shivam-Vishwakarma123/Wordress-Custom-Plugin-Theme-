<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wpshivam' );

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
define( 'AUTH_KEY',         'Qmc5,Bf9a]A[IC~bwYS/S>#.1ZZy.Z_x8y_^nVzo:*:DB/F1-@-~_kJuE_1+UxMw' );
define( 'SECURE_AUTH_KEY',  '32Rjn<jMu?(EaXC5Q>nlP[fA@TT`Ou7XKyEDk~I|Z`_Yr>*;a>=YFNDN%zDQyMoZ' );
define( 'LOGGED_IN_KEY',    'HLeh$1p:zfdIh#H} 9Aoc9cr6N:G?x.VE(;E$l31iq/ DB|9[{L8T%hqLtmxsH?r' );
define( 'NONCE_KEY',        '2dp(dju8T7m#F6M`(1m$&^IYFE @g,[!X.XaFsTch-|-e>MTu$d7srDguClWlH-$' );
define( 'AUTH_SALT',        '.zD!/*M6?-U#ekC|VeaRq)[8/niF C2U^a/YN_hO+,pe^c>ZO#p<<d:LyO}F^W-H' );
define( 'SECURE_AUTH_SALT', 'T2o=;>wU3.1_vyEva937a_7u9JIr!g{|}c@z_K<9FP8mH$h+Pr]da+YLJ4j`x2O9' );
define( 'LOGGED_IN_SALT',   '1x*}6,[Ww_@jlCe)`SEr1TS^`4{2p2Zum4r:p}6C,]#FX]|:iD1C_+*VT) ;9,o1' );
define( 'NONCE_SALT',       ',F-8y^ ZfX&k*&]W)-Ps@m87avaBi&N$;[}f3D.~,/0g5rk[DPPxEQ a6>{7 1Hd' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
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
