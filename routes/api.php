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
     Route::post('signin', 'AuthController@login');
    Route::post('sendOTP','AuthController@checkmobile');
    Route::post('verifyOTP','AuthController@verifyOTP');
    Route::post('create-user', 'AuthController@signup');
    Route::post('create-profile', 'AuthController@createProfile')->middleware('auth:api');
    Route::post('reset', 'AuthController@resetPassword');
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
            Route::get('doodle/requests','DoodleController@doodleRequests');
             Route::get('userinfo','UserController@getUserforEditScreen');
       
    });

     Route::group([
       'middleware' => 'auth:api',
        'prefix' => 'settings',
    ], function() {
        Route::get('privacy/status','SettingsController@getPrivacy');
        Route::post('privacy/status', 'SettingsController@setPrivacy');
        Route::get('privacy/username','SettingsController@getUsername');
      Route::get('privacy/checkUsername','SettingsController@checkUsername');
        Route::post('privacy/username','SettingsController@setUsername');
        Route::post('account/password','SettingsController@setNewPassword');

       
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
  Route::delete('/picture/remove', 'UserController@removeProfilePic');
    Route::get('followers','UserController@followers');
    Route::get('following','UserController@following');
     Route::get('{id}/checkFollowing','UserController@checkFollowing');
     Route::get('{id?}/doodles','DoodleController@getDoodles');
    Route::post('{id?}/doodles','DoodleController@storeDoodles');
     Route::get('suggetions','UserSuggetions@index');
     Route::post('suggetions','UserSuggetions@index');
    Route::post('{id}/bio/like','BioLikesController');
      Route::post('sync', 'UserSuggetionController@store');
      Route::get('view/followers/{id}','UserController@seeFollowers');
      Route::get('view/following/{id}','UserController@seeFollowing');

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
    Route::post('{comment}/comments/like', 'CommentController@like');
    Route::get('{id}/likes','PostController@likes');
    Route::delete('/{id}/delete/comment','CommentController@destroy');
    Route::get('comment/{comment_id}/replies','CommentController@getReplies');
    Route::post('comment/{comment_id}/replies/','CommentController@storeReplies');
    Route::delete('comment/{comment_id}/replies/{reply_id}','CommentController@deleteReplies');


});

      Route::group([
      'prefix' => 'suggetions'
    ], function() {
        Route::post('sync', 'UserSuggetionController@store')->middleware('auth:api');
    });




});


