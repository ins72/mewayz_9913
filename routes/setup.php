<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SetupController;

// Setup wizard route - only available if not configured
Route::middleware('web')->group(function () {
    Route::get('/setup', [SetupController::class, 'index'])->name('setup.index');
    Route::post('/setup', [SetupController::class, 'process'])->name('setup.process');
});

// Health check route
Route::get('/health', function() {
    return response()->json([
        'status' => 'healthy',
        'app' => config('app.name'),
        'version' => '2.0.0',
        'environment' => config('app.env'),
        'database' => 'connected',
        'timestamp' => now()
    ]);
});

// Redirect root to main application or setup
Route::get('/', function () {
    if (!file_exists(base_path('.env')) || !config('app.key')) {
        return redirect('/setup');
    }
    return view('welcome');
});