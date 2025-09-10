<?php
/* SSL Settings */
define('FORCE_SSL_ADMIN', true);

/* Turn HTTPS 'on' if HTTP_X_FORWARDED_PROTO matches 'https' */
if (strpos($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') !== false) {
    $_SERVER['HTTPS'] = 'on';
}

if ( !isset( $_SERVER['HTTPS'] ) ) {
    $_SERVER['HTTPS'] = 'on';
}
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
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'admin_subnhanhixnet');

/** Database username */
define('DB_USER', 'admin_wp_j2tfr');

/** Database password */
define('DB_PASSWORD', 'f4q@ujS#o&E73ju^');

/** Database hostname */
define('DB_HOST', 'localhost:3306');

/** Database charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

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
define('AUTH_KEY', '(46G9~z!D8aW4RM3P~4258bv933tT8(1y1@98s4j6*9ey4gI52@KG28Tj(9huL4A');
define('SECURE_AUTH_KEY', 'QFr#Y+@4p;@]8-5*OD0_FpNN8p5KfzABG683ok#(l77)kom)--x(4_y53l2]Z866');
define('LOGGED_IN_KEY', 'o8P3S0K6[t/o5uI||d3]SG-T55|2v_~GFy03|ZuuON[Wf[#1s4lB;Zy7:Qo0d8;:');
define('NONCE_KEY', 'C)4xrB0:-WMr-k_m*koa4K-~l@7B]!Os|~D/P#G&5hN|]4b[KEA&qMOf)|||#@ln');
define('AUTH_SALT', '89V7Yk6R3hb@%5(;(#)(14!+gPDqPf35l7@-I:XvR9+RCF5c4h42cP;Ptz8b1Pp2');
define('SECURE_AUTH_SALT', 'h_lPBMGYud93a9#WM8F&87&08~18m4H~X84Y7JLE(1D!/VTZk~6mLp-2[j@fmCKs');
define('LOGGED_IN_SALT', '707OFH#u5G!3u2&9W:#7317n@aBT;+8Bv7V874_z%qnfZ%sM|:x1m@wv@1blJh9Y');
define('NONCE_SALT', 'QxD7-M5*(lb|i5TDE:O!xyhGA1083*8!f-:-4P92Ul+z1ik9!9+;2WvMbIUk|8o6');


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'gfNxY_';


/* Add any custom values between this line and the "stop editing" line. */

define('WP_ALLOW_MULTISITE', true);


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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */

// if( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
//     wp_die( '0', 400 );
// }

if ( ! defined( 'WP_DEBUG' ) ) {
define( 'WP_DEBUG', false );           // Tắt chế độ debug
define( 'WP_DEBUG_LOG', false );       // Tắt ghi lỗi vào file debug.log
define( 'WP_DEBUG_DISPLAY', false ); 
}     // Tắt hiển thị lỗi trên giao diện



define( 'WP_CACHE_KEY_SALT', '1f640f14d3a7659cffbef7b860c112ef' );
define( 'DISABLE_WP_CRON', true );
define( 'WP_REDIS_PREFIX', 'wp_1f640f14d3a7659cffbef7b860c112ef' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
