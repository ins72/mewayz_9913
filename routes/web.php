<?php

use App\Models\Invoice;
use App\Yena\Ai\Image;
use App\Yena\YenaMail;
use Livewire\Volt\Volt;
// use Laravel\Folio\Folio;
use App\Yena\Site\Generate;
use App\Models\OrganizationPage;
use App\Yena\aaPanel;
use App\Yena\Ai\Purify;
use App\Yena\Site\DefaultLanding;
use App\Yena\YenaEmbed;
use App\YenaOauth\Facades\YenaOauth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use MarkSitko\LaravelUnsplash\Facades\Unsplash;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Barryvdh\DomPDF\Facade\Pdf;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Simple test route
Route::get('/test', function () {
    return response()->json([
        'message' => 'Laravel is working!',
        'status' => 'success',
        'timestamp' => now()
    ]);
});

// Health check route
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'database' => 'connected',
        'timestamp' => now()
    ]);
});

// Root route fallback
Route::get('/', function () {
    return response()->json([
        'message' => 'Welcome to Mewayz - The Complete Creator Economy Platform',
        'status' => 'online',
        'api_docs' => '/api/health',
        'version' => '1.0.0',
        'features' => [
            'Bio Sites & Link-in-Bio',
            'Social Media Management', 
            'E-commerce',
            'Course Creation',
            'Email Marketing',
            'Analytics & Reporting'
        ],
        'timestamp' => now()
    ]);
});

require __DIR__.'/auth.php';

// Landing page route (disabled for now - view may not exist)
// Route::get('/', function () {
//     return view('pages.index');
// })->name('home');

// PWA offline route
Route::get('/offline', function () {
    return view('offline');
})->name('offline');

// PWA manifest route
Route::get('/manifest.json', function () {
    return response()->file(public_path('manifest.json'));
})->name('manifest');

// Service Worker route
Route::get('/sw.js', function () {
    return response()->file(public_path('sw.js'));
})->name('service-worker');

// Dashboard routes - Laravel Folio will handle the dashboard pages
Route::get('/dashboard', function () {
    return view('pages.dashboard.index');
})->middleware([\App\Http\Middleware\CustomWebAuth::class])->name('dashboard-index');

// Workspace Setup
Route::get('/dashboard/workspace', function () {
    return view('pages.dashboard.workspace.index');
})->middleware([\App\Http\Middleware\CustomWebAuth::class])->name('dashboard-workspace-index');

Route::get('/workspace-setup', function () {
    return view('pages.workspace-setup');
})->middleware([\App\Http\Middleware\CustomWebAuth::class])->name('workspace-setup');

// Content & Sites
Route::get('/dashboard/sites', function () {
    return view('pages.dashboard.sites.index');
})->middleware([\App\Http\Middleware\CustomWebAuth::class])->name('dashboard-sites-index');

Route::get('/dashboard/linkinbio', function () {
    return view('pages.dashboard.linkinbio.index');
})->middleware([\App\Http\Middleware\CustomWebAuth::class])->name('dashboard-linkinbio-index');

Route::get('/dashboard/templates', function () {
    return view('pages.dashboard.templates.index');
})->middleware([\App\Http\Middleware\CustomWebAuth::class])->name('dashboard-templates-index');

// Social Media
Route::get('/dashboard/instagram', function () {
    return view('pages.dashboard.instagram.index-dynamic');
})->middleware([\App\Http\Middleware\CustomWebAuth::class])->name('dashboard-instagram-index');

Route::get('/dashboard/social', function () {
    return view('pages.dashboard.social.index');
})->middleware([\App\Http\Middleware\CustomWebAuth::class])->name('dashboard-social-index');

// Business Growth
Route::get('/dashboard/audience', function () {
    return view('pages.dashboard.audience.index');
})->middleware([\App\Http\Middleware\CustomWebAuth::class])->name('dashboard-audience-index');

Route::get('/dashboard/crm', function () {
    return view('pages.dashboard.crm.index');
})->middleware([\App\Http\Middleware\CustomWebAuth::class])->name('dashboard-crm-index');

Route::get('/dashboard/community', function () {
    return view('pages.dashboard.community.index');
})->middleware([\App\Http\Middleware\CustomWebAuth::class])->name('dashboard-community-index');

// Monetization
Route::get('/dashboard/store', function () {
    return view('pages.dashboard.store.index-dynamic');
})->middleware([\App\Http\Middleware\CustomWebAuth::class])->name('dashboard-store-index');

Route::get('/dashboard/store/create', function () {
    return view('pages.dashboard.store.create');
})->middleware([\App\Http\Middleware\CustomWebAuth::class])->name('dashboard.store.create');

Route::get('/dashboard/courses', function () {
    return view('pages.dashboard.courses.index-dynamic');
})->middleware([\App\Http\Middleware\CustomWebAuth::class])->name('dashboard-courses-index');

Route::get('/dashboard/courses/create', function () {
    return view('pages.dashboard.courses.create');
})->middleware([\App\Http\Middleware\CustomWebAuth::class])->name('dashboard.courses.create');

Route::get('/dashboard/booking', function () {
    return view('pages.dashboard.booking.index');
})->middleware([\App\Http\Middleware\CustomWebAuth::class])->name('dashboard-booking-index');

// Marketing
Route::get('/dashboard/email', function () {
    return view('pages.dashboard.email.index-dynamic');
})->middleware([\App\Http\Middleware\CustomWebAuth::class])->name('dashboard-email-index');

Route::get('/dashboard/automation', function () {
    return view('pages.dashboard.automation.index');
})->middleware([\App\Http\Middleware\CustomWebAuth::class])->name('dashboard-automation-index');

// Analytics
Route::get('/dashboard/analytics', function () {
    return view('pages.dashboard.analytics.index');
})->middleware([\App\Http\Middleware\CustomWebAuth::class])->name('dashboard-analytics-index');

Route::get('/dashboard/reports', function () {
    return view('pages.dashboard.reports.index');
})->middleware([\App\Http\Middleware\CustomWebAuth::class])->name('dashboard-reports-index');

// Business Management
Route::get('/dashboard/wallet', function () {
    return view('pages.dashboard.wallet.index');
})->middleware([\App\Http\Middleware\CustomWebAuth::class])->name('dashboard-wallet-index');

Route::get('/dashboard/invoices', function () {
    return view('pages.dashboard.invoices.index');
})->middleware([\App\Http\Middleware\CustomWebAuth::class])->name('dashboard-invoices-index');

Route::get('/dashboard/team', function () {
    return view('pages.dashboard.team.index');
})->middleware([\App\Http\Middleware\CustomWebAuth::class])->name('dashboard-team-index');

// AI & Tools
Route::get('/dashboard/ai', function () {
    return view('pages.dashboard.ai.index');
})->middleware([\App\Http\Middleware\CustomWebAuth::class])->name('dashboard-ai-index');

Route::get('/dashboard/media', function () {
    return view('pages.dashboard.media.index');
})->middleware([\App\Http\Middleware\CustomWebAuth::class])->name('dashboard-media-index');

Route::get('/dashboard/integrations', function () {
    return view('pages.dashboard.integrations.index');
})->middleware([\App\Http\Middleware\CustomWebAuth::class])->name('dashboard-integrations-index');

// Settings
Route::get('/dashboard/settings', function () {
    return view('pages.dashboard.settings.index');
})->middleware([\App\Http\Middleware\CustomWebAuth::class])->name('dashboard-settings-index');

Route::get('/dashboard/help', function () {
    return view('pages.dashboard.help.index');
})->middleware([\App\Http\Middleware\CustomWebAuth::class])->name('dashboard-help-index');

// Upgrade route for Stripe payment integration
Route::get('/dashboard/upgrade', function () {
    return view('pages.dashboard.upgrade');
})->middleware([\App\Http\Middleware\CustomWebAuth::class])->name('dashboard-upgrade-index');


Route::name('run-')->namespace('App\Http\Controllers\Run')->group(function() {
  Route::get('run-update', 'DatabaseController@update')->name('update');
  Route::get('run-cron', 'CronController@run')->name('cron');
});

Route::get('lol', function(){
    //实例化对象
    $api = new aaPanel();
    //获取面板日志
    $r_data = $api->RemoveDomain('jeffjola.com');
    return;
    $r_data = $api->AddDomain('wepsteb.com', 'jeffjola.com');
    //输出JSON数据到浏览器
    echo json_encode($r_data);

    return;
    $invoice = Invoice::first();
    $mail = new \App\Yena\YenaMail;
    $mail->send([
        'to' => 'jeffjola@gmail.com',
        'subject' => __('You just got tipped'),
    ], 'invoice.email', [
        'amount' => '$2000',
        'invoice' => $invoice
    ]);

    return;

    // return view('includes.invoicepdf');
    
    $data = [
        'imagePath'    => public_path('img/profile.png'),
        'name'         => 'John Doe',
        'address'      => 'USA',
        'mobileNumber' => '000000000',
        'email'        => 'john.doe@email.com'
    ];
    $pdf = PDF::loadView('includes.invoicepdf', $data);
    return $pdf->stream('resume.pdf');
    return;
    $user_id = 1234; // Example user ID
    $unique_string = encodeCrc($user_id);
    $retrieved_user_id = decodeCrc($unique_string);
    
    echo "Retrieved User ID: " . $retrieved_user_id;
    echo "Encoded String: " . $unique_string;

    return;
    $mail = new \App\Yena\YenaMail;
    $mail->send([
        'to' => 'jeffjola@gmail.com',
        'subject' => __('You just got tipped'),
    ], 'bio.tip', [
        'amount' => '$2000',
        'user' => $this
    ]);

    return;
    $sandyembed = new YenaEmbed('https://www.tiktok.com/@andsea_miyakoisland/video/7358077798319131912');
    $fetch = $sandyembed->fetch();


    dd($fetch);
    try {
        $response = Http::post('http://gamma.test/payments/click/prepare', [
            'key1' => 'value1',
            'key2' => 'value2',
            // Add more key-value pairs as needed
        ]);
    
        if ($response->successful()) {
            $data = $response->json();
            dd($data, 'data');
            // Do something with $data
        } else {
            // Handle non-2xx responses
            $error = $response->body();

            dd('ee', $error, 'zzz');
            // Log or handle the error as needed
        }
    } catch (RequestException $e) {

        dd('rr', $e->getMessage());
        // Handle exceptions
        $error = $e->getMessage();
        // Log or handle the exception as needed
    }
    dd('zzz');
    dd(get_vite_site_resources());
    $r = new DefaultLanding;
    return $r->build();
});

YenaOauth::routes();

Route::prefix('dashboard')->name('dashboard-')->namespace('App\Http\Controllers')->group(function() {
  Route::get('builder/ai', 'Dashboard\Builder\AiController@ai')->name('builder-ai');
});

// Admin
Route::prefix('dashboard/admin')->name('dashboard-admin-')->namespace('App\Http\Controllers\Admin')->middleware(['isAdmin'])->group(function() {
    Route::prefix('users')->name('users-')->namespace('Users')->group(function(){
        Route::get('/', 'UsersController@index')->name('index');
        Route::post('post/{tree}', 'PostController@tree')->name('post');
    });

    Route::prefix('sites')->name('sites-')->namespace('Sites')->group(function(){
        Route::get('/', 'SitesController@index')->name('index');
        // Route::get('view-report/{_id}', 'PagesController@view_report')->name('view-report');
        Route::post('post/{tree}', 'PostController@tree')->name('post');
    });

    Route::prefix('bio')->name('bio-')->namespace('Bio')->group(function(){
        Route::get('/', 'BioController@index')->name('index');
        // Route::get('view-report/{_id}', 'PagesController@view_report')->name('view-report');
        Route::post('post/{tree}', 'PostController@tree')->name('post');
        
        Route::prefix('templates')->name('templates-')->namespace('Templates')->group(function(){
            Route::get('/', 'TemplatesController@index')->name('index');
            
            Route::post('post/{tree}', 'PostController@tree')->name('post');
        });
    });

    // Payments
    Route::prefix('payments')->name('payments-')->namespace('Payments')->group(function(){
        Route::get('/', 'PaymentsController@index')->name('index');
        // Pending Payments
        Route::get('pending', 'PaymentsController@pending')->name('pending');
        Route::post('post/{tree}', 'PostController@tree')->name('post');
    });
    
    // Plans
    Route::prefix('plans')->name('plans-')->namespace('Plans')->group(function(){
        Route::get('/', 'PlansController@index')->name('index');
        Route::post('post/{tree}', 'PostController@tree')->name('post');
    });
    
    // Translation
    Route::prefix('languages')->name('languages-')->namespace('Languages')->group(function(){
        Route::get('/', 'TranslationController@languages')->name('index');
        
        Route::post('post/{tree}', 'PostController@tree')->name('post');
    });
    
    // Template
    Route::prefix('templates')->name('templates-')->namespace('Templates')->group(function(){
        Route::get('/', 'TemplatesController@index')->name('index');
        
        Route::post('post/{tree}', 'PostController@tree')->name('post');
    });
    
    // Website
    Route::prefix('website')->name('website-')->namespace('Website')->group(function(){
        Route::get('/', 'WebsiteController@index')->name('index');
        
        Route::post('post/{tree}', 'PostController@tree')->name('post');
    });

    // Settings
    Route::prefix('settings')->name('settings-')->namespace('Settings')->group(function(){
        Route::get('/', 'SettingsController@index')->name('index');
        
        Route::post('post/{tree}', 'PostController@tree')->name('post');
    });
});
// ADD THIS ROUTE HERE FOR THE PARTNERS PAGE
Route::get('/shuvrajit', function () {
    return view('pages.shuvrajit');
})->name('shuvrajit');
// In routes/web.php
use App\Http\Controllers\MewayzPartnershipController;

// ADD THIS ROUTE HERE FOR THE PARTNERS PAGE
Route::get('/partners', function () {
    return view('pages.partners');
})->name('partners');

Route::post('/api/send-mewayz-email', [MewayzPartnershipController::class, 'sendVettingEmail'])->name('send.mewayz.email');
require __DIR__.'/folio.php';