<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LegalController;

/*
|--------------------------------------------------------------------------
| Legal Routes
|--------------------------------------------------------------------------
|
| Here is where you can register legal routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "legal" middleware group. Make something great!
|
*/

// Legal Pages
Route::get('/terms-of-service', [LegalController::class, 'termsOfService'])->name('legal.terms-of-service');
Route::get('/privacy-policy', [LegalController::class, 'privacyPolicy'])->name('legal.privacy-policy');
Route::get('/cookie-policy', [LegalController::class, 'cookiePolicy'])->name('legal.cookie-policy');
Route::get('/refund-policy', [LegalController::class, 'refundPolicy'])->name('legal.refund-policy');
Route::get('/accessibility-statement', [LegalController::class, 'accessibilityStatement'])->name('legal.accessibility-statement');

// GDPR and Compliance API Routes
Route::middleware('auth:sanctum')->prefix('api/legal')->group(function () {
    Route::post('/cookie-consent', [LegalController::class, 'handleCookieConsent']);
    Route::post('/data-export', [LegalController::class, 'requestDataExport']);
    Route::post('/data-deletion', [LegalController::class, 'requestDataDeletion']);
    Route::delete('/data-deletion/{requestId}', [LegalController::class, 'cancelDataDeletion']);
    Route::get('/data-processing', [LegalController::class, 'getDataProcessingActivities']);
    Route::get('/audit-log', [LegalController::class, 'getAuditLog']);
});

// Public cookie consent (for non-authenticated users)
Route::post('/api/cookie-consent', [LegalController::class, 'handleCookieConsent']);