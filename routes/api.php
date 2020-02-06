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

Route::post('uploadfile',function(){

$fileName = "user_image.jpg";
$path = request()->file('photo')->move(public_path("/"),$fileName);
$photoURL = url('/',$fileName);
return $photoURL;



});


Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'AuthController@login')->middleware('throttle:4,5');
    Route::post('sendOTP','AuthController@checkmobile');
    Route::post('verifyOTP','AuthController@verifyOTP');
    Route::post('create-user', 'AuthController@signup');
    Route::put('create-profile', 'AuthController@createProfile');
    Route::post('reset', 'AuthController@resetPassword')->middleware('throttle:4,10');
    Route::post('new-password', 'AuthController@newPassword')->middleware('throttle:5,10');
     Route::get('user', 'AuthController@user')->middleware('auth:api');

    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
    });


     Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::get('search', 'UserController@search');
    });

});