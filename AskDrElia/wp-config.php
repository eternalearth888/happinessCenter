<?php
// ** MySQL settings ** //
define('DB_NAME', 'db244889187');    // The name of the database
define('DB_USER', 'dbo244889187');     // Your MySQL username
define('DB_PASSWORD', 'MHXb5sTD'); // ...and password
define('DB_HOST', 'db1494.perfora.net');    // 99% chance you won't need to change this value
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

// Change SECRET_KEY to a unique phrase.  You won't have to remember it later,
// so make it long and complicated.  You can visit http://api.wordpress.org/secret-key/1.0/
// to get a secret key generated for you, or just make something up.
define('SECRET_KEY', '>p>{\\Gh[QE1Gjf9g1orz! >E[)tSn[[S=t}0Ikxj\\?@DBpyg3E:?BG4!{1Kv0+2:');

// You can have multiple installations in one database if you give each a unique prefix
$table_prefix  = 'wp_';   // Only numbers, letters, and underscores please!

// Change this to localize WordPress.  A corresponding MO file for the
// chosen language must be installed to wp-content/languages.
// For example, install de.mo to wp-content/languages and set WPLANG to 'de'
// to enable German language support.
define ('WPLANG', '');

/* That's all, stop editing! Happy blogging. */

define('ABSPATH', dirname(__FILE__).'/');
require_once(ABSPATH.'wp-settings.php');
?>
