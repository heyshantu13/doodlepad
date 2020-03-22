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


// Refresh FCM Token

Route::put('refreshFCM','UserController@refreshFCMid')->middleware('auth:api');


Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('sendOTP','AuthController@checkmobile');
    Route::post('verifyOTP','AuthController@verifyOTP');
    Route::post('create-user', 'AuthController@signup');
    Route::post('create-profile', 'AuthController@createProfile')->middleware('auth:api');
    Route::post('reset', 'AuthController@resetPassword')->middleware('throttle:5,5');
    Route::post('new-password', 'AuthController@newPassword')->middleware('throttle:5,5');
    Route::get('user', 'AuthController@user')->middleware('auth:api');
    Route::get('checksession','AuthController@checksession')->middleware('auth:api');

    Route::group([
       'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'UserController@user');
        Route::get('getUserDetails/{id}', 'UserController@getUser');
        Route::get('activities', 'PostActivityController@index')->name('activities.index');
          Route::get('requests', 'RequestActivityController@index')->name('activities.index');
           Route::post('requests/accept', 'RequestActivityController@acceptRequest')->name('activities.accept');
            Route::post('requests/reject', 'RequestActivityController@rejectRequest')->name('activities.reject');
       
    });

     Route::group([
       'middleware' => 'auth:api',
        'prefix' => 'settings',
    ], function() {
        Route::get('status','SettingsController@status');
        Route::post('privacy/status', 'SettingsController@updateStatus');
        Route::post('account/username','SettingsController@updateUsername');
        Route::post('account/password','SettingsController@updatePassword');
        Route::post('notification/onLike','SettingsController@updateLikeStatus');
         Route::post('notification/onComment','SettingsController@updateCommentStatus');

       
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

      Route::group([
          'prefix' => 'profile',
    'middleware'=>'auth:api',
],function(){
    Route::post('{user}/follow', 'UserController@follow');
    Route::get('followers','UserController@followers');
    Route::get('following','UserController@following');
     Route::get('{id}/checkFollowing','UserController@checkFollowing');
});

      Route::group([
    'prefix' => 'posts',
    'middleware'=>'auth:api',
],function(){
   Route::get('{id}/get', 'UserController@userPosts');
    Route::post('new', 'PostController@createPost');
    Route::get('myposts','PostController@myPosts'); 
    Route::delete('{id}/delete','PostController@destroy');
    Route::patch('{id}/pin','PostController@pinned');
    Route::get('all','PostController@index');
    Route::post('{post}/like', 'PostController@like');
    Route::get('{post}/show', 'PostController@show');
    Route::get('view/{post}/like','PostController@viewLikes');
    Route::get('{post}/comments', 'CommentController@index');
    Route::post('{post}/comments', 'CommentController@store');
    Route::post('comments/{comment}/like', 'CommentController@like');
});





});


