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
define('DB_NAME', 'admin_subnhanhicnet');

/** Database username */
define('DB_USER', 'admin_wp_2x9bj');

/** Database password */
define('DB_PASSWORD', 'X*wBryc0!K2JfH5S');

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
define('AUTH_KEY', 'Cs_P#TEC1ZAKuxn0Y0G@me(Y*RUPn3l6mfrD3S3To4nC/tDL3G+2)+GR6xygn7JH');
define('SECURE_AUTH_KEY', 'k~]KMJgW30VMr2X1:]U*8MH/Sy_27XD1X37Hc3D6ehzX8SNrbZpYl)i[ZvImJ927');
define('LOGGED_IN_KEY', 'F23wJ~d3Qve00[XVEj]m[5FORT/g7tee9GYGf6_-X%kX%3I:3s~9ML9-B8nAGV/1');
define('NONCE_KEY', '!]EaQX36t~u7xPn2xtkn[+q:P:czO5_]A*#+:!ax_fF6XVO;Mmn0SJ;V_pIl;8N!');
define('AUTH_SALT', 'ft8|_5v/G%+q+v;]~6|0t18Oc0*/vPp3s;bz2JGq9c32LT[B@hr[/x%v187z00T_');
define('SECURE_AUTH_SALT', 'l5L8340)%7p8!**!&Q]o~89JB9NczJEP0_PnP9(4uRm#z-@HhC6Ylmf12N@5F839');
define('LOGGED_IN_SALT', '53Wj]0595M%F7r61t3(A@c3UJ4&(w_sN5M3l-c1m7:692n7kaBzJ)#[k4BF&gyVs');
define('NONCE_SALT', '[08C[xC#P23_SV07x9_1|F]8y1)9[dqga6h3*;w@A/7[[QIN]w21]92)S7P32AUZ');


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

if( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
<<<<<<< HEAD
    wp_die( '0', 400 );
=======
  wp_die( '0', 400 );
>>>>>>> 8244566785abdcfc7ddba0897fa341cc450cf6d7
}

if ( ! defined( 'WP_DEBUG' ) ) {
define( 'WP_DEBUG', false );           // Tắt chế độ debug
define( 'WP_DEBUG_LOG', false );       // Tắt ghi lỗi vào file debug.log
define( 'WP_DEBUG_DISPLAY', false ); 
}     // Tắt hiển thị lỗi trên giao diện



define( 'WP_CACHE_KEY_SALT', '9b6c019f4f6a94c5b063f7eb776972dd' );
define( 'DISABLE_WP_CRON', true );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
