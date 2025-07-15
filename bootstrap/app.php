<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

// Define QR constants only if not already defined
if (!defined('QR_CACHEABLE')) {
    define('QR_CACHEABLE', false);
}
if (!defined('QR_CACHE_DIR')) {
    define('QR_CACHE_DIR', false);
}
if (!defined('QR_LOG_DIR')) {
    define('QR_LOG_DIR', false);
}

if (!defined('QR_FIND_BEST_MASK')) {
    define('QR_FIND_BEST_MASK', false);
}
if (!defined('QR_FIND_FROM_RANDOM')) {
    define('QR_FIND_FROM_RANDOM', 2);
}
if (!defined('QR_DEFAULT_MASK')) {
    define('QR_DEFAULT_MASK', 2);
}
                                              
define('QR_PNG_MAXIMUM_SIZE',  1024);   
 
// Encoding modes
 
define('QR_MODE_NUL', -1);
define('QR_MODE_NUM', 0);
define('QR_MODE_AN', 1);
define('QR_MODE_8', 2);
define('QR_MODE_KANJI', 3);
define('QR_MODE_STRUCTURE', 4);
// Levels of error correction.
define('QR_ECLEVEL_L', 0);
define('QR_ECLEVEL_M', 1);
define('QR_ECLEVEL_Q', 2);
define('QR_ECLEVEL_H', 3);

// Supported output formats

define('QR_FORMAT_TEXT', 0);
define('QR_FORMAT_PNG',  1);

 
define('QRSPEC_VERSION_MAX', 40);
define('QRSPEC_WIDTH_MAX',   177);

define('QRCAP_WIDTH',        0);
define('QRCAP_WORDS',        1);
define('QRCAP_REMINDER',     2);
define('QRCAP_EC',           3);

define('QR_IMAGE', true);

define('STRUCTURE_HEADER_BITS',  20);
define('MAX_STRUCTURED_SYMBOLS', 16);

define('N1', 3);
define('N2', 3);
define('N3', 40);
define('N4', 10);

define('QR_VECT', true);

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;
