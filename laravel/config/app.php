<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    'currency' => 'USD',
    'defaultStorage' => '5',
    'unsplashQuality' => 'regular', // raw, full, regular, small, thumb, small_s3
    'pexelsQuality' => 'large', // original, large2x, large, medium, small, tiny, potrait, landscape
    'bio_address' => 'bio.app',
    'bio_prefix' => '@',
    'mediakit_prefix' => 'mediakit',
    'aapanel_enable' => env('AAPANEL_ENABLE'),
    'aapanel_api' => env('AAPANEL_API'),
    'aapanel_host' => env('AAPANEL_API_HOST'),
    'aapanel_website' => env('AAPANEL_WEBSITE'),

    'wallet' => [
        'defaultMethod' => env('WALLET_METHOD', 'stripe'),
        'currency' => env('WALLET_CURRENCY', 'USD'),
        'percentage' => env('WALLET_PERCENTAGE', 0),
        'withdraw_percentage' => env('WALLET_WITHDRAW_PERCENTAGE', 0),
    ],

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', true),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    'asset_url' => env('ASSET_URL'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    'faker_locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY', 'base64:+2GvGrVJqhWz0oHKjtYUEcbXVAnYkKZjLlvpSNhC89U='),

    'cipher' => 'AES-256-CBC',
    'openai_key' => env('OPENAI_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => 'file',
        // 'store' => 'redis',
    ],

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => ServiceProvider::defaultProviders()->merge([
        /*
         * Package Service Providers...
         */
        browner12\helpers\HelperServiceProvider::class,
        Jenssegers\Agent\AgentServiceProvider::class,
        Darryldecode\Cart\CartServiceProvider::class,
        MarkSitko\LaravelUnsplash\UnsplashServiceProvider::class,
        Lab404\Impersonate\ImpersonateServiceProvider::class,
        Amirami\Localizator\ServiceProvider::class,
        'Camroncade\Timezone\TimezoneServiceProvider',

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        App\Providers\FolioServiceProvider::class,
        App\Providers\VoltServiceProvider::class,
        App\Providers\SiteServiceProvider::class,
        App\YenaOauth\YenaOauthServiceProvider::class,
        App\Yena\Pexels\PexelsServiceProvider::class,
    ])->toArray(),

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => Facade::defaultAliases()->merge([
        'Country'   => \App\Yena\Country::class,
        'Currency'  => \App\Yena\Currency::class,
        'Cart'      => Darryldecode\Cart\Facades\CartFacade::class,
        'Unsplash'  => MarkSitko\LaravelUnsplash\Facades\Unsplash::class,
    ])->toArray(),

    /* 
    /
    /
    /
    /
    /
    /
    */

    'HELPCENTER_URL' => env('HELPCENTER_URL', '#'),

    'site_prefix' => env('SITE_PREFIX', ''),
    'enable_registration' => env('enable_registration', true),
    'email_verification' => env('email_verification', false),
    'APP_INSTALL' => env('APP_INSTALL'),
    'INSTALLED' => env('INSTALLED'),
    'SESSION_DRIVER' => env('SESSION_DRIVER'),
    'APP_VERSION' => env('APP_VERSION'),
    'USE_DEFAULT_LOCALE' => env('USE_DEFAULT_LOCALE'),
    'MAIL_MAILER' => env('MAIL_MAILER'),
    'MAIL_HOST' => env('MAIL_HOST'),
    'MAIL_PORT' => env('MAIL_PORT'),
    'MAIL_USERNAME' => env('MAIL_USERNAME'),
    'MAIL_PASSWORD' => env('MAIL_PASSWORD'),
    'MAIL_ENCRYPTION' => env('MAIL_ENCRYPTION'),
    'MAIL_FROM_ADDRESS' => env('MAIL_FROM_ADDRESS'),
    'MAIL_FROM_NAME' => env('MAIL_FROM_NAME'),
    'APP_LOCALE' => env('APP_LOCALE'),
    'APP_EMAIL' => env('APP_EMAIL'),


    //
    'APP_DEBUG_CSS' => env('APP_DEBUG_CSS'),

    'FILESYSTEM' => env('FILESYSTEM'),
    'AWS_ACCESS_KEY_ID' => env('AWS_ACCESS_KEY_ID'),
    'AWS_SECRET_ACCESS_KEY' => env('AWS_SECRET_ACCESS_KEY'),
    'AWS_DEFAULT_REGION' => env('AWS_DEFAULT_REGION'),
    'AWS_BUCKET' => env('AWS_BUCKET'),
    'AWS_ASSETS' => env('AWS_ASSETS'),

    'GOOGLE_ENABLE' => env('GOOGLE_ENABLE'),
    'GOOGLE_CLIENT_ID' => env('GOOGLE_CLIENT_ID'),
    'GOOGLE_SECRET' => env('GOOGLE_SECRET'),
    'GOOGLE_CALLBACK' => env('GOOGLE_CALLBACK'),

    'FACEBOOK_ENABLE' => env('FACEBOOK_ENABLE'),
    'FACEBOOK_CLIENT_ID' => env('FACEBOOK_CLIENT_ID'),
    'FACEBOOK_SECRET' => env('FACEBOOK_SECRET'),
    'FACEBOOK_CALLBACK' => env('FACEBOOK_CALLBACK'),

    'RECAPTCHA_SITE_KEY' => env('RECAPTCHA_SITE_KEY'),
    'RECAPTCHA_SECRET_KEY' => env('RECAPTCHA_SECRET_KEY'),
];
