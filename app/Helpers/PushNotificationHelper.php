<?php
/**
* AUthor: Shantanu Kulkarni
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
        // $token = "dfic9akzTik:APA91bGBrpmTeXgCcxGEMCmqiY41SkMcqDO1zFbnRAO36sxaRi57glRXrSxDGqW2cqGpy3xfjbmclJZP6hErkYRmAcaR8eeg4uOfCpQO6kcNMBUCPt0D1d2zlpPwyZSKOiNd_z8TNUqr";

        // echo $token;

  FCM::sendTo($token, $option, $notification, $data);
    }
}
