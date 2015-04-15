<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link http://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'campiers_wo1015');

/** MySQL database username */
define('DB_USER', 'campiers_wo1015');

/** MySQL database password */
define('DB_PASSWORD', 'EFjXhQKXrk8O');

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
define('AUTH_KEY', 'C^<cn}j$ZGCZRMHx!yHw*dsPllOmYTJ|jHa<skJqDrzO|nSkGabScJ+&kw;jbV!}NuV/HY=SY[TQaRCf?;[Fk!<ca?(qOn@{@jcHxT<Eqr<QhVnP_+r__ZpZtN=jf]Ti');
define('SECURE_AUTH_KEY', 'GzCRufbzL|obHdmZUsq!]*=)M?aGojB*]IvZmxG-YNkcyl&)-a@+B!rBPPqbZ*c[Q&FN$-EvUbNDRmSI)=|VXVkHju[/yS%svr?gwUDi/lkcYfj%[^%I(mU[Wwb)mqm$');
define('LOGGED_IN_KEY', '{a<aptp<VpaSiqtVmt+/*CFI=^u)kGq)%DrSAC/}f=;;vzQY@{*Wdn>-Fhc)/!?u}}*ew}zRg)v]qcB+ywaC&uGqZn->sIBXQk?J};yh?FxuZzo*yifHY%RPAF<n|Swv');
define('NONCE_KEY', 'R*Z?U&Bw@%L/=/NdRa&rEv?uBNLprnb*EeQvpDSx>E*C[{O<qKJKLL([[UwJ-zBc=@PnQEZXxike&mn^k?Eg<|i/Ol>@EJI@OwMQx%CI;IMWZ?LfJeop+Q%>Ukdoi<^*');
define('AUTH_SALT', 'Adnvi=fB*jgYxh-dlJ%%;W@TKbEoZO/<qugLf/zDvGjYV)(+nI]XOyaPJS(SklvGeNu%nI}b{EHgo%Y%oDZ-|NmmQMK[&sd|?)T+GrB(i<Y<INJHaFXsfA;BVaScE___');
define('SECURE_AUTH_SALT', 'uG-t-mL>N!q^ESbg!@DKZ;wPCDyFRw/<Y+<V%VFs^SFTqO>h=NPi]cd_N/qU[w)SSVB}@Dbi!KDxXXxv+uj)ml-wT*uQd}s(D&MOpoy)<}(gN;LzD=mFPCdine]z|{E^');
define('LOGGED_IN_SALT', '*kIashyMjat=@vdwP({+!-TeK;|mBbs}cx=fK&c$N)zK$[DpLZmFWDK(A!_[R?J]SUFK>J+O<tbPiCirkFi%uswZRlF%%Db%LZ/KavdXZQvIy$GIQPV>ZtmT;JPrYR$i');
define('NONCE_SALT', 'JJeveB};^iu]Z|oo&qLZqbHSk^YAruLe_|bDCXSKBjopgl%&G^]gXl^j|/VzigsC)@+)RRc+C!kxcCX/M@[M[yJi@)=jRRWpjXSk{t;+?>fLc&|AA-pAcODXjZkSJl+T');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_fqvv_';

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

/**
 * Include tweaks requested by hosting providers.  You can safely
 * remove either the file or comment out the lines below to get
 * to a vanilla state.
 */
if (file_exists(ABSPATH . 'hosting_provider_filters.php')) {
	include('hosting_provider_filters.php');
}
