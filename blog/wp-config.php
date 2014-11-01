<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'devscola');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

define ('WPLANG', 'es_ES');
/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'lUy-eUFbs5o69E=3Wuyl C<Zmp&JPLpjeON}z?<2v|.,}:3X?%g =u.oAY_[1^J$');
define('SECURE_AUTH_KEY',  'D 8EoX~m@guQSa3VaZ7wbC8Z;/2Wd/*&Qp+pJc|FpUYT+vEQU)X$199<QUoj*@`~');
define('LOGGED_IN_KEY',    'ys)Uabrv&wfEY(%J}gx-%Ma(Azc5G|UxRj)w9~@|`:{=)%429( i/P>8+h,e_&h6');
define('NONCE_KEY',        'fR^[U,!uflY|SP<ra2Ymk?^Y#>+yIThX+{eh#PL&|Hi?C|SU(b$9Aj#2Ph2FKY%X');
define('AUTH_SALT',        'cpvpP IeSDO;D4xfACY,skVAKrJI/)vN(Ql+rhUY7u8|mZ kCk}mP*#Vl|/=JRkx');
define('SECURE_AUTH_SALT', 'Kq}$bA1@$sY|(s/56?d&;XT$8=TJZpQ&~`4yANbpXi3iSm f.|J!Y-3pR!=M|QL=');
define('LOGGED_IN_SALT',   't.a|~9=|t9X^CzZ3xkC!%l7|owLBTq&(`No*=%|`+!6V.k3sO||$Mv6~!]Iq.YMW');
define('NONCE_SALT',       'Y2+J<cn,|J=HZf&6ZAeMi^OExY-(N8v8Y?=eGeuIn0f$9R=+5/eaABO ?~:S|Z6Y');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
