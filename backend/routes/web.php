<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\OAuthController;

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

// OAuth Routes (for web redirects)
Route::prefix('auth')->group(function () {
    Route::get('google', [OAuthController::class, 'redirectToProvider'])->defaults('provider', 'google');
    Route::get('google/callback', [OAuthController::class, 'handleProviderCallback'])->defaults('provider', 'google');
    Route::get('facebook', [OAuthController::class, 'redirectToProvider'])->defaults('provider', 'facebook');
    Route::get('facebook/callback', [OAuthController::class, 'handleProviderCallback'])->defaults('provider', 'facebook');
    Route::get('apple', [OAuthController::class, 'redirectToProvider'])->defaults('provider', 'apple');
    Route::get('apple/callback', [OAuthController::class, 'handleProviderCallback'])->defaults('provider', 'apple');
});

// Landing page route
Route::get('/', function () {
    return view('pages.landing');
});

// Simple test route
Route::get('/test', function () {
    return 'Laravel is working!';
});

// Flutter app route
Route::get('/app', function () {
    return response()->file(public_path('flutter.html'));
});

// Mobile app route alias
Route::get('/mobile', function () {
    return response()->file(public_path('flutter.html'));
});

// Flutter app direct route
Route::get('/flutter.html', function () {
    return response()->file(public_path('flutter.html'));
});

// Include auth routes
require __DIR__.'/auth.php';