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
   
   $token = "foddpo9iR5Oi7FKQzclHAk:APA91bG8ddZD9FvdYDBWECkOu9_3br12f0BIA_8AmSrNH-IgHUkwSq7W_8Mz4rC80Z9e96nEkYYvqqqeLw91aV3o3xtiESWxaKHIbLXY_RznGJXbOF2jXn51vtpqHshLWQq-C_Li859L";
   $title = "Web notification";
   $body = "This is Web notification";
        $data['title'] = $title;
        $data['body'] = $body;
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);

        $notificationBuilder = new PayloadNotificationBuilder($title);
        $notificationBuilder->setBody($body)
            ->setSound('default')
            ->setClickAction("com.example.doodlepad_TARGET_NOTIFICATION");

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData($data);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();
       

  FCM::sendTo($token, $option, $notification, $data);
         
});






Auth::routes([
    'register' => false, // Registration Routes...
    'reset' => false, // Password Reset Routes...
    'verify' => false, // Email Verification Routes...
    'login' => false
]);





