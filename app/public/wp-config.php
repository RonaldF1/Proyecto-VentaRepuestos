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
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',          '^>rN8+G7Kns%:4in=(!-]aN)`/^<4NGm(qY@]~HNr=1Fpoa|gS_=@2r$1Vb2Al|C' );
define( 'SECURE_AUTH_KEY',   'e)0?;V__!?B5y|G!pZI 8}+HiL0Cl ^C_l#7T_T~2y/[n(m]wW`[Ss14n;Q504(_' );
define( 'LOGGED_IN_KEY',     'c@#!2[UO`!$^3vtTf|+rI=aRFvK8`+<hErZE&jJ,:7.i]n?tb,UU~iRxF]DAE0Mt' );
define( 'NONCE_KEY',         ';?sll7}0#0o)2]X$Xzj0.P||q6dvX:&~G{,~aZM:V~Y^L^-{8Oc2+umw>u}FKYV(' );
define( 'AUTH_SALT',         '8/f>G`*li@rS 1dSuvAm/R!A8nE4<m]}Ep3}JDE1G]Bjmh7Y4uC,ixaC#twnL0|I' );
define( 'SECURE_AUTH_SALT',  '-hUd/&?u46Zk;G@QgIf%|K^o5pk8]b343wN,Lg/t9ei@qfkZ{hQ7V..Ce|Pj WDX' );
define( 'LOGGED_IN_SALT',    'F&as`]Q@$!aIGNMx<`wvO~yyOC;`>*OX<g.?f1eZ{E1!BpSP9$&VHq,${%{q{.2b' );
define( 'NONCE_SALT',        'ZlZj5t5X~:bK{k{9$W#O@7{SAeD<GL^u.=JAHJ?!/XiqmwMev*xn5M;T~f4%4<N,' );
define( 'WP_CACHE_KEY_SALT', 'A/>4cU!MUnz+U)=yK_O)l^rY }73UQNG`n/zb}aEuvj8T}OaY2BD^CfH>S,_V2u7' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
