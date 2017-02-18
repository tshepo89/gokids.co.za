<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'gokids');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'nK1~I54mnemD=C<zBbrd~vESl~c,Z|[1R$Yz[#3A:p0jyVlMN,7H|){Mk6t1LYWi');
define('SECURE_AUTH_KEY',  'R}-79UtWd,|]xs3bu,qgY_L-#7Uq!_~RTo+M&81:Y0AGr~D9O}5UrPwG@=2ocfv+');
define('LOGGED_IN_KEY',    ':(-RUMgf=Lgz.3dM%>t]gV<DNlH2A$Uy][utOgCUy.K1/9:j~~p _D5%@wSuqlEk');
define('NONCE_KEY',        'e:AEiiATl{+?|x}JVOegJMG?fN3e/NIsUOC9:fFT:|-(YODgJ4&c11|Uza}/!mh>');
define('AUTH_SALT',        'u#*5:nzP*avRyZ.ODq1y,Ni52^uasNy5^0*40p)aX#%!1>4|Cf>ZCr[ S5l}`~~U');
define('SECURE_AUTH_SALT', 'k+`52-0LbyA{Hyzv-Y.HMsJ5zA-(oQu~yi4:`6iJ8!0{_ ?[]lRO/HA-{?>nJw2B');
define('LOGGED_IN_SALT',   'l9&1r#x_ybm*bf__W__xCPT1uAc~K?8+B?*sKv50@x~-Bn1<ab$%4l0pN 9.5-N)');
define('NONCE_SALT',       '{MHtMI9[HNQ&s]rx9z WJ5.!ea3YJ2%_B3_U.{BlEmN@a[U$hkP]Us#6gep8sNGu');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
