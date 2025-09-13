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

define('DB_NAME', 'admin_motphimchillday');

/** Database username */
define('DB_USER', 'admin_wp_wkbes');

/** Database password */
define('DB_PASSWORD', '!65^g2X_qSBMZTk2');

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
define('AUTH_KEY', 'K[)V!dGng*nL*6]t(k8v@i7ceO!Wg/EN/q@o5JYNLs/4v]Ar2V66L#)6AD[pZ&_D');
define('SECURE_AUTH_KEY', '7!4qDI-2uJu:|I8+]ZWcp%QVIc2b:82tF!UvL2@4|hgn23W*WC@[32@X3FZ[6AU1');
define('LOGGED_IN_KEY', 'ur4l[8&bD39wO%)QtSZM!N3p7FJ3hJ9H_7G[8t#@)@]nSz/qAelDJg_P1ARQ5L6J');
define('NONCE_KEY', 'Sq4q#1Dn8:2#Y+I9+U_0h4K(n-0T4X8J:h_c;PG2+T81)CkV9J8Z9V|ua45:Y1t3');
define('AUTH_SALT', '9):+|P]#it|]qg|39_*L19[I4i2CzOn4Uxx4CB9vP39Q9i[14*%:qsVks8X2|di9');
define('SECURE_AUTH_SALT', 'f0f~|0yVF(lhc_+&kp7*89nRmH_78Ej2;~4/V/4L!P63-fv[)K!_R8IaGwP;D139');
define('LOGGED_IN_SALT', 'uY89+1lvc&+O@49v0b9)4Yv131wML@4t70@2/DZ)%[;-]9/0*p3[7SbV_:t0jsU[');
define('NONCE_SALT', 'QO4c!)S)0+Y8(Z+U!G-ZsK1tW~c&3B52;6a3IpM4*l5|m6zljGN10-/88R;St282');


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

if( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
    wp_die( '0', 400 );
}

// Định nghĩa WP_DEBUG trước
if ( ! defined( 'WP_DEBUG' ) ) {
    define( 'WP_DEBUG', false );
    define( 'WP_DEBUG_LOG', false ); // Ghi lỗi vào tệp debug.log
    define( 'WP_DEBUG_DISPLAY', false ); // Không hiển thị lỗi trên giao diện
}



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


define( 'WP_CACHE_KEY_SALT', 'de97c0b0efa083af7be419efeb121ddf' );
define( 'WP_REDIS_PREFIX', 'wp_de97c0b0efa083af7be419efeb121ddf' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
