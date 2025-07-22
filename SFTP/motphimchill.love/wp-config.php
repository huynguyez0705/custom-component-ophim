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

define('DB_NAME', 'admin_motphimchilllove');

/** Database username */
define('DB_USER', 'admin_wp_raoc9');

/** Database password */
define('DB_PASSWORD', '%OshotfnOQgz11$9');

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
define('AUTH_KEY', '5%~(#l0]x9gHSEh~]1~MLoKxQd99Xr]R:-0]ft]jp;6r[4ZB!24AU;1[5F/0+(TH');
define('SECURE_AUTH_KEY', 'E7;~:05#Lhdtn:53J93/0-_O2mmLD68b)5nOSdm43c_0Kv*+d8Q@N%Z763MSZtd/');
define('LOGGED_IN_KEY', '9w99ZkE3V086CCn(~:A3JK3L[QMd7f+Z10czwLARsGC&N(cd;/&7F]slGb8C/8;7');
define('NONCE_KEY', 'ggm1yRq_[4h8m6NT|D@L-Z998I8&h2QHQ1UIio/Q)43cyz&EAy8-I~Z|wTd:zkc(');
define('AUTH_SALT', 'pV6c5a5g0t6L&7JX18Lq%*S5*&pBG9jUjr9Bhd7H@4rVLC1)g/A9:u:W7s8LT4|J');
define('SECURE_AUTH_SALT', 'V6XNf-eCK+~3NBX8!7ciI1r;BIS[x8h35-*(vRT;l7MK3c94i%I615k38-YKx8F4');
define('LOGGED_IN_SALT', 'T#Mf[#16])N6)so%HMU_H~:3gkT*m[#f0/*ME0I4|0*@Dsgk]wU6gu*:)ccjQ%JD');
define('NONCE_SALT', '9mBV%Brupe!57TNP9HZCx262GGT#/TW5I@J4k@1+S/~8(!Im4KK*67P|ddH9xgo[');


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

// Định nghĩa WP_DEBUG trước
if ( ! defined( 'WP_DEBUG' ) ) {
    define( 'WP_DEBUG', true );
    define( 'WP_DEBUG_LOG', true ); // Ghi lỗi vào tệp debug.log
    define( 'WP_DEBUG_DISPLAY', true ); // Không hiển thị lỗi trên giao diện
}

// if( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
//   wp_die( '0', 400 );
// }
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


define( 'WP_CACHE_KEY_SALT', 'da710c2ad0b39b9f2ede2efd04e39dfd' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
