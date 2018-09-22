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
define('DB_NAME', 'Concept360');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', '127.0.0.1');

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
define('AUTH_KEY',         'c^V-9C7Sv$Z)n1L^*dShz|e#Wq(fB*>U%8Yb/7n[I dYe&<!|lFhld*B0#}U-ga6');
define('SECURE_AUTH_KEY',  '>uy:99-<U{n%2wnZ9<>LNeHeO{)BX%*Z@;63yCo_PvGc5Mr8(F7Xl1zH{?!+SD,=');
define('LOGGED_IN_KEY',    'o9|&Dq{A%%#ob=6~Rtyg::Lbg$LBux+&)(1vWY]`O*Ga&RA,eQ95&GRvS/=fXFRh');
define('NONCE_KEY',        'J($lh;&t*cd<|`MZsxpuD&_w3#Vc$Zdr1ei1@lWo0M`O)Jz8?ZF,Z#gQ1Y^S#aS/');
define('AUTH_SALT',        'YD)s?(mA#?vsq|bCdlQOZop4DtCl`;YL~N-,0z#7TnsrF=Q`!kMd0[(WLiOq;b/U');
define('SECURE_AUTH_SALT', 'WZvsTHLuGyK:/MXEMENKOW7]bu}W@]>B~>Bdb,o-]RYOW:>vka2ia313J6!II`}/');
define('LOGGED_IN_SALT',   'n6Y7o}gCUYz@r{7YA}fvt*NBM)xs|#PzXO!Mz)vOK<`V44jKGUcz*`>&*Ani75.G');
define('NONCE_SALT',       'k+=$IBEq[%>9ip492$ H+f6pQftD?NSSl,&&$:L47`6)V_DO5eLm$9JtQb,j_$fk');

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
define('WP_DEBUG', true);
define( 'WP_DEBUG_DISPLAY', true );

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
