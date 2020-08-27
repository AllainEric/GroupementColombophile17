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
define('AUTH_KEY',         'kHNTUMZlAqJUSPUdLV+x6TtYfJXxho0SK1svEUISfjo7CAQ6P47NRDfofD521dyyLicEomR4Jl7lQ//wGwpX1Q==');
define('SECURE_AUTH_KEY',  'BWGgWXz+U6ohVKy0ntQnmZgE5RLmUNTdCz2LJQVDJAeKI1dWEjz9I06OKbNGN2frsoaEvQbwLYYXGABZjqQ68A==');
define('LOGGED_IN_KEY',    'tzhqSRv3g8C7K5O1PRyDNttWOajcu5rLg2QM5Aa/zbGNXBPyw6FjX6gfMCDmmsniE3LmsP6g80f8oUEg9Bv9Ng==');
define('NONCE_KEY',        'HvJaZyJBAlzl09ArtRSwMDM5Jy3y9iSGpcegm37cA7JgBcZ/dkYN2IIOHer2cy4Luh7JmoMNTwGjqbuCfgmnIw==');
define('AUTH_SALT',        'fsQK968QFi/BcyLhpEQXz2qX9pXuCmbyd+//5HMrFJQKCiD8JeZ9gm253gYQddMSn+f6ajDTTkmIXdCtTqzteQ==');
define('SECURE_AUTH_SALT', '8CxMyqEqCja0cAw2dOQwAEI/Pu/YYqqtSsXH2u76ZXxh9EwbHh8278rrBgCc1+RqO6Hapwls297SEyhFkoMyFg==');
define('LOGGED_IN_SALT',   '0T7jcye5ckLBOeJ5o6Kpk3PKJwd9u0RbqLFaQ5nWIgCp4Ss8autc7GoN2GM4tH7tvxnttmf/abvVRT0lxQowLw==');
define('NONCE_SALT',       'evoEQs51WWaoN6COgHCa8t0c9T1bBRsuJpBTyqMKCQa6rUghuf+YcbmTcuwg0/yOM6+WJYiCKyVCRlXLWSaCfQ==');

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
