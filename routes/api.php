<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('sendOTP','AuthController@checkmobile')->middleware('throttle:3,1');
    Route::post('resendOTP','AuthController@checkmobile')->middleware('throttle:3,1');
    Route::post('verifyOTP','AuthController@verifyOTP')->middleware('throttle:3,1');
    Route::post('create-account', 'AuthController@signup');
    Route::post('create-profile', 'AuthController@signup')->middleware('auth:api');
  
    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
    });
});