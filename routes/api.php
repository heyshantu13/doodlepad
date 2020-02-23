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

// Route::post('/do','UserController@getinfo');


// Refresh FCM Token

Route::put('refreshFCM','UserController@refreshFCMid')->middleware('auth:api');
 Route::post('profile/follow/{userProfile}', 'UserController@follow')->middleware('auth:api');

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('sendOTP','AuthController@checkmobile');
    Route::post('verifyOTP','AuthController@verifyOTP');
    Route::post('create-user', 'AuthController@signup');
    Route::post('create-profile', 'AuthController@createProfile')->middleware('auth:api');
    Route::post('reset', 'AuthController@resetPassword')->middleware('throttle:4,10');
    Route::post('new-password', 'AuthController@newPassword')->middleware('throttle:5,10');
    Route::get('user', 'AuthController@user')->middleware('auth:api');
    Route::get('checksession','AuthController@checksession')->middleware('auth:api');

    Route::group([
       'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'UserController@user');
        Route::get('getUserDetails/{id}', 'UserController@getUser');
       
    });


     Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::get('search', 'UserController@search');
    });

      Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::post('changeProfile', 'UserController@updateProfile');
        
    });

});


// Manage Posts

Route::group([
    'prefix' => 'posts',
    'middleware'=>'auth:api',
],function(){
    Route::post('/new', 'PostController@createPost');
    Route::get('myposts','PostController@myPosts');
    Route::delete('/post/{id}','PostController@deletePost');

});