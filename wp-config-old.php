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
define('DB_NAME', 'wordpress');

/** MySQL database username */
define('DB_USER', 'wordpress');

/** MySQL database password */
define('DB_PASSWORD', 'ap4twpi1');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

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
define('AUTH_KEY',         '9Dd`+B&YX!,Q-||-2g52}Ew3KyF>XpS+f_g@v1UOYVXji,Sgy@w#m;B7nc=Ft9C%');
define('SECURE_AUTH_KEY',  'Cv-OIotQ4jhFYbV[dnwqj0&kU|@Ii!ss6~f=Ug@I3=3=uoM]BlLmv#E(z]J#QYjo');
define('LOGGED_IN_KEY',    'zRr+SQ([^J]AVHJR|%C-7d}]-|M_QI,Q^iZ7+BkPX0Oqc/#zz>|jPZ H+_{L}H`^');
define('NONCE_KEY',        'rE:$&S#S|Utu3]5Wnp,eNwC%rGh_44L<gU( H7cgx+Y{o|5XgZlaK1pq-`;GQxpp');
define('AUTH_SALT',        'f|9a )Fey wrO`6%|!ic|M l%AMTF(cFB&Ev]ax-r}>zLW8e-y,-vOHe_Nby!%Ti');
define('SECURE_AUTH_SALT', '6A!yM|&0!jM^j3Fb8nx`DAmmDP|sV ot)-J6m5|T`ZB%U|KB%,#F*29RK]4&z4+l');
define('LOGGED_IN_SALT',   '5z VNE,/`uhk;++L2,s{n<j=,R#u~BnHs0-t&!2C5:sU`aK$[<8(/PI0_gCA7i_.');
define('NONCE_SALT',       'fmE7i{U002=WChkvbr7%F-i^J]qLm%8`9+%Ivb+3i8{p,;1Od=0Aegy+:KMFa~wc');

/**#@-*/

/* Multisite */
define( 'WP_ALLOW_MULTISITE', true );

define('WP_HOME','http://www.itercom.org');
define('WP_SITEURL','http://www.itercom.org');

/* Added to nudge in multisite */
define('CONCATENATE_SCRIPTS', false);

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
# $table_prefix  = 'wp_'; /* baseline */
$table_prefix  = 'icc_'; /* new for security */

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

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
