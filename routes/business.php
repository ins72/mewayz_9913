<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BusinessController;

/*
|--------------------------------------------------------------------------
| Business Routes
|--------------------------------------------------------------------------
|
| Here is where you can register business routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "business" middleware group. Make something great!
|
*/

// Business Pages
Route::get('/about', [BusinessController::class, 'about'])->name('business.about');
Route::get('/features', [BusinessController::class, 'features'])->name('business.features');
Route::get('/pricing', [BusinessController::class, 'pricing'])->name('business.pricing');
Route::get('/contact', [BusinessController::class, 'contact'])->name('business.contact');
Route::get('/blog', [BusinessController::class, 'blog'])->name('business.blog');
Route::get('/blog/{slug}', [BusinessController::class, 'blogPost'])->name('business.blog.post');
Route::get('/case-studies', [BusinessController::class, 'caseStudies'])->name('business.case-studies');
Route::get('/case-studies/{slug}', [BusinessController::class, 'caseStudy'])->name('business.case-study');
Route::get('/testimonials', [BusinessController::class, 'testimonials'])->name('business.testimonials');
Route::get('/careers', [BusinessController::class, 'careers'])->name('business.careers');
Route::get('/partners', [BusinessController::class, 'partners'])->name('business.partners');
Route::get('/press-kit', [BusinessController::class, 'pressKit'])->name('business.press-kit');
Route::get('/security', [BusinessController::class, 'security'])->name('business.security');
Route::get('/status', [BusinessController::class, 'status'])->name('business.status');
Route::get('/help-center', [BusinessController::class, 'helpCenter'])->name('business.help-center');
Route::get('/sitemap', [BusinessController::class, 'sitemap'])->name('business.sitemap');

// API Routes
Route::prefix('api/business')->group(function () {
    Route::post('/contact', [BusinessController::class, 'submitContact']);
});