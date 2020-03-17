<?php
 /*
                   Send Push Notification To User

                    Author: Shantanu Kulkarni

                    Github: heyshantu13

 */

namespace App\Helpers;


use LaravelFCM\Facades\FCM;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;

class PushNotificationHelper
{
    static function send($token, $title, $body, $data)
    {
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
    }
}
