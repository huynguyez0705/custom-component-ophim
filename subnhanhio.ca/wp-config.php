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
define('DB_NAME', 'admin_subnhanhioca');

/** Database username */
define('DB_USER', 'admin_wp_o7tmp');

/** Database password */
define('DB_PASSWORD', 'rtbL230JB9~VZMw*');

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
define('AUTH_KEY', '31/XamKuV;STgMk~(1!/777O7bo5_1#0z+a1)s/*5G]453|@M|e&-2~*e713d&)z');
define('SECURE_AUTH_KEY', 'eZ~#!b23fRE[w8!;tMR0Ba[(BQ6@~7s2+kcr6LFF1#&jS/&Nom_hWk62zt|!+*]g');
define('LOGGED_IN_KEY', 'n7I*@a2BD00O7H6-Lh5@xVCE/+Gwmz9ywL6C3Dm!n3]ZDd[9&sgBpa~ko)exb&&1');
define('NONCE_KEY', 'W46%KYq(8&BH_D6!tJ5BjM(X(L2#tYWR1Vb5i-O])gI6yUZj7q#n!gSz*n89yOUt');
define('AUTH_SALT', '#i6uz6a~X!FnAm*%WCV/9%fX4j8AnzDAe/:z389]&T[6g]Ui&Kg2Zgl[Op1YXzW#');
define('SECURE_AUTH_SALT', 'I6nV3[pj+hJH:/(2-bae(%Wk7s[81Z_/H7%@~bkh6H7V)07cU(@9*85R::j9G2WZ');
define('LOGGED_IN_SALT', '1-7|05c*Wv*]VC0@E@_D]PG0(n-q/02LkIM_6~E3TX/62sVd7g%ixm)J#ZB07-Fh');
define('NONCE_SALT', '6~ysW98;7[!6~;b5_NH9;rC8h2896L3gXFxVy010nt2Mcr|%+8q|BDL4/O0eXqW0');


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
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_CACHE_KEY_SALT', 'a5c69f81e6b04783b01f2f0a899f50de' );
define( 'DISABLE_WP_CRON', true );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
