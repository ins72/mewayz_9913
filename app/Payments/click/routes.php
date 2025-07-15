<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('verify/{sxref}', 'Controllers\ClickController@verify')->name('verify');
Route::post('prepare', 'Controllers\ClickController@prepare')->name('prepare');
Route::post('complete', 'Controllers\ClickController@complete')->name('complete');

Route::get('lol', function(){

    dd('zzz');
});
// Route::post('webhook', 'Controllers\StripeController@webhook')->name('webhook');