<?php
//Begin Really Simple SSL key
define('RSSSL_KEY', 'NShMS1hKvEHhsoU5ciItcRitSbYu5sOmzmXHsPCnWdgMSYFpjZ2OqEwFDNf8yU7W');
//END Really Simple SSL key

//Begin Really Simple SSL session cookie settings
@ini_set('session.cookie_httponly', true);
@ini_set('session.cookie_secure', true);
@ini_set('session.use_only_cookies', true);
//END Really Simple SSL cookie settings
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
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
define( 'DB_NAME', 'wefixsg_wp728' );

/** Database username */
define( 'DB_USER', 'wefixsg_wp728' );

/** Database password */
define( 'DB_PASSWORD', '2pOK[JAJc)Nf@8};' );

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
define('AUTH_KEY', 'm8Q0WAZMKYMVfqNgZKj6Ra5pdBaoA-zs_N8_sOT4pqFmpairTHvne3rn2zVnu37272G9FkeXzf7Z_cHKFPFBJw');
define('SECURE_AUTH_KEY', '53W2jRpZ39L34do_J76ovkiL2mdoZ-tHrzPcGv0lxbrSYVPZL8ZIA8wGM_f1IDnPDPM6P1OCLKvGMUynVlnoBQ');
define('LOGGED_IN_KEY', '9gm_z0IjJ7r2KrciDTMrprbRk5kvE4aQ6191qlLpoFEzlWWX63qjY1a0mtc5wCXNQvxyk2-AZQxgGNpk5gkLgw');
define('NONCE_KEY', 'jBFP6lTvtVuvZdmpQclWStgZq7Ez7w2ifhbdoqorFpl2WHl5inRI_CiLcF2VyS25ulm5APfpwqQ3YU8CYiEwag');
define('AUTH_SALT', 'IqjQrgL651PRlysu9p5C9iTFFDKR6gO-_92C6kp-spQrSCI1D9YCcSAobXuTu15XKlAoKJHSnYy6dAdqNtr6yw');
define('SECURE_AUTH_SALT', 'wdhley86X1-xdTh17oL7Q42yt-faVEhlsSkP7z2rOZjmqWpU7X2i2zC3agtnEDUyPNtzvpM1HwJygxU9dGGZ0A');
define('LOGGED_IN_SALT', '9rzM8kQoTnG-KBran3HP1x-fWjZ3XqTk7es2qk--lzfwu0X0kKN9oDYPMzbArBwonYZp0axq0YzoSuDZeN0ZBA');
define('NONCE_SALT', 't2MViF7pgq3mvlniLKGFz5MrMF1IQE3UaVM4EIL3duoFIB2Us74NtteY3iNZsROae0NcUkKvxXV9H2fPnCyx1Q');

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wpra_';

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



define('DISALLOW_FILE_EDIT', true);
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
