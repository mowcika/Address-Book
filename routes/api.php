<?php

use Illuminate\Http\Request;

use App\Http\Controllers\Api\MobileApi;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


// Assistant Service api
//paytm token

Route::post('/MobileApi', [MobileApi::class, 'dynamicServerJson']);
