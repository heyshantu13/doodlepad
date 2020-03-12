<?php

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

use LaravelFCM\Facades\FCM;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;



Route::get('/', function(){
   
   $optionBuilder = new OptionsBuilder();
$optionBuilder->setTimeToLive(60*20);

$notificationBuilder = new PayloadNotificationBuilder('New Follower');
$notificationBuilder->setBody('@heyshantu started following you.')
				    ->setSound('default')
				    ->setClickAction('ChatWindowActivity');


$dataBuilder = new PayloadDataBuilder();
$dataBuilder->addData();

$option = $optionBuilder->build();
$notification = $notificationBuilder->build();
$data = $dataBuilder->build();

$token = "eGZkMbpBqpg:APA91bGivYo0aCIRLFYN9TQt7z1Mvvf3O7h75w7Rdw6c46XPgut-rqHOwgaa6T9OWQZvMby2dB1f2MW88iWCOb7g0KbJmcnlAtMujRJ57JxtvfSA-UadSefryvzbZVXerguqmJ0youNp";

$downstreamResponse = FCM::sendTo($token, $option, $notification, $data);

$downstreamResponse->numberSuccess();
$downstreamResponse->numberFailure();
$downstreamResponse->numberModification();

// return Array - you must remove all this tokens in your database
$downstreamResponse->tokensToDelete();

// return Array (key : oldToken, value : new token - you must change the token in your database)
$downstreamResponse->tokensToModify();

// return Array - you should try to resend the message to the tokens in the array
$downstreamResponse->tokensToRetry();

// return Array (key:token, value:error) - in production you should remove from your database the tokens
$downstreamResponse->tokensWithError();


});






Auth::routes([
    'register' => false, // Registration Routes...
    'reset' => false, // Password Reset Routes...
    'verify' => false, // Email Verification Routes...
    'login' => false
]);



Route::get('/do','UserController@getinfo');

