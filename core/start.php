<?php
if ( ! function_exists( 'add_action' )) { exit; }

/*
|--------------------------------------------------------------------------
| Time Stamp App
|--------------------------------------------------------------------------
|
| Set the app boot time to the current time in seconds since the Unix epoch
|
*/
define( 'TR_START', microtime( true ) );

/*
|--------------------------------------------------------------------------
| Version
|--------------------------------------------------------------------------
|
| Set the version for TypeRocket using the style major.minor.patch
|
*/
define( 'TR_VERSION', '3.0' );

/*
|--------------------------------------------------------------------------
| Configuration
|--------------------------------------------------------------------------
|
| Load configuration file.
|
*/
$tr_config_file = realpath( __DIR__ . '/../config.php' );
if( ! file_exists($tr_config_file)) {
    die('Add a the file at ' . $tr_config_file);
}

require $tr_config_file;

/*
|--------------------------------------------------------------------------
| Require Core Classes
|--------------------------------------------------------------------------
|
| Require the core classes of TypeRocket.
|
*/
spl_autoload_register( function ( $class ) {

    $prefix   = 'TypeRocket\\';
    $base_dir = __DIR__ . '/../src/';
    $app = defined('TR_APP_FOLDER_PATH') ? TR_APP_FOLDER_PATH . '/' : __DIR__ . '/../app/';

    $len = strlen( $prefix );
    if (strncmp( $prefix, $class, $len ) !== 0) {
        return;
    }

    $relative_class = substr( $class, $len );

    $file = str_replace( '\\', '/', $relative_class ) . '.php';
    $app =  $app . $file;
    if (file_exists( $base_dir . $file )) {
        require $base_dir . $file;
    } elseif( file_exists( $app )) {
        require $app;
    }
} );

/*
|--------------------------------------------------------------------------
| Loader
|--------------------------------------------------------------------------
|
| Load TypeRocket
|
*/
new \TypeRocket\Config();
require __DIR__ . '/functions.php';
new \TypeRocket\Core(true);

/*
|--------------------------------------------------------------------------
| Run Registry
|--------------------------------------------------------------------------
|
| Runs after hooks muplugins_loaded, plugins_loaded and setup_theme
| This allows the registry to work outside of the themes folder. Use
| the typerocket_loaded hook to access TypeRocket from your WP plugins.
|
*/
add_action( 'after_setup_theme', function () {
    do_action( 'typerocket_loaded' );
    \TypeRocket\Registry::initHooks();
} );

/*
|--------------------------------------------------------------------------
| Add APIs
|--------------------------------------------------------------------------
|
| Add slim REST and Matrix APIs.
|
*/
require __DIR__ . '/api/endpoints.php';

define( 'TR_END', microtime( true ) );