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
define( 'DB_NAME', 'w_db' );

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
define( 'AUTH_KEY',         'tTp#::!N{ej%:0HU(/_vg.2bwF+2FhQfc8Hhof%O+IoiUqK?jh(-fOoQ$@/|4*C.' );
define( 'SECURE_AUTH_KEY',  '-B.R)pC0RpRcLXN.8TrxB1$EQCynFpDlG}Yz^72q[>Wo|v4fAsgwA$yC}C$qVQ$p' );
define( 'LOGGED_IN_KEY',    'gEQfga)v/.GOT{DJ~7[NggjYqMP7q1{U369AD1yf`<6$0@P$FtcnnO]a9=96^&=o' );
define( 'NONCE_KEY',        '[:Ad<eVwsH{3TfHl*%1[3Vz;^&@.SsJQ3vm&d#j?Q_e*@N:08!`g=/<sZLH:Go6g' );
define( 'AUTH_SALT',        'F{p{&_qC3+-Ka.TP]#}}e/VH}^Xz/=z9`z1Od[e<bR9`O6h1u8H!{T`j77=Ec>yO' );
define( 'SECURE_AUTH_SALT', 'Fm*pp<nS)18CS,.p5*.Q.Uyh$,O%0zDhClOEGi+02E,y~Irk*!RA?N BM6o0#5t&' );
define( 'LOGGED_IN_SALT',   '$baBG]]]z]CF>&mKy3@Fr%$R[VY9t,z.{~C(rmlJKoTR~7z^0V6Oo%am6:b_,)UY' );
define( 'NONCE_SALT',       'dc&XC~vcg$jAh,fG>>R|L!4%vU%rGbx[`N^rgo4E9Y0N19f7XQzXIaXAMo@``vZ_' );

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
