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
define( 'DB_NAME', 'local' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'rI/iw/6e4k57IKVTIscRIvtdWfM3vLxT+9QK3nw3H5LvTfQPoOQyDqWAfAv24j/vngqFaX5NgIL6prEJP9aKiw==');
define('SECURE_AUTH_KEY',  'SSdlHyI3ziStpydX53KKI2Z6/5/f4jzzbqmf433KqGbTLwFCc1uY34fgJNdws7MWR/sVIp+yHDRlNZf23lDl+A==');
define('LOGGED_IN_KEY',    'GmjqmR+Gw9JhSRyz8fgpkCPN74LfnIXAAAHVhzm6L4YCKneaA+edXKIIXSPEQZxghKlN7RDqR/n5PrBehSjTIg==');
define('NONCE_KEY',        '3wqavE244dlsU8t13D9XetrqMCYcD5cRh3JXtgZq5sXVnf60tpeTb5Ko0WV0yrOehlUT6g08c8RVgk+0qG0FIw==');
define('AUTH_SALT',        'n1mtJSfA4rDrEXmOkTH6fsm8HCiKbstesQtWihbhBAQ5WYKOyZBFeoNAaadRPmkSfo+PtjLlxAjqD1Z2LFowxw==');
define('SECURE_AUTH_SALT', 'XP7EEffiCL7a71T0yRGvll9z08bXNhwglrYu1gpZKuklA1GqkwzjKAwU8nkyswjNjcp6E2lrYXnDmwd9ThDehw==');
define('LOGGED_IN_SALT',   '7tT9OWUpuI7SUzRpOfAlFc40a+sLdTa/1pARGmSHwIOUNyaSXdBo0DKnBiDJ/UkMeMo1LuCTCDxvPIP42gbtpw==');
define('NONCE_SALT',       'XeSHicUHpKM3MxUHhQhkmK4TDHGXbFAdTzyRlMuYd0Z/Cmn8MXzkCtsGD4OebR1hg+myJMlz8S4W9D7Ifznv0w==');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';




/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
