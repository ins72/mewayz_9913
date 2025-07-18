<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InstallController;

/*
|--------------------------------------------------------------------------
| Installer Routes
|--------------------------------------------------------------------------
|
| These routes handle the automated installation wizard for the Mewayz
| platform. They guide users through system setup, database configuration,
| and initial admin user creation.
|
*/

Route::prefix('install')->name('install.')->group(function () {
    Route::get('/', [InstallController::class, 'index'])->name('index');
    Route::get('/step/{step}', [InstallController::class, 'step'])->name('step');
    Route::post('/process/{step}', [InstallController::class, 'processStep'])->name('process');
    Route::get('/status', [InstallController::class, 'status'])->name('status');
    Route::post('/reset', [InstallController::class, 'reset'])->name('reset');
});