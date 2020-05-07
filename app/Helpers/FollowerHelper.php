<?php

 /*
                   Create Follower Activity

                    Author: Shantanu Kulkarni

                    Github: heyshantu13

            */

namespace App\Helpers;

use App\UserProfile;
use App\RequestActivity;
use App\FollowerActivity;
use App\User;

class FollowerHelper
{
    static function FollowerActivity($followerid, $userid, $type)
    {
         $username = User::where('id',$followerid)->first(['username']);

         $follower_activity = new FollowerActivity();
         $follower_activity->follower_id = $followerid;
         $follower_activity->user_id = $userid;
   

        $title = "Doodlepad";
        $body = null;
        $data = array();
        switch($type) {
            case config('constants.USER_FOLLOW_FOLLOWING'):
                
                $body =  "@".$username->username." started following you.";
                $data = ["user_id" => $userid];
                $follower_activity->TYPE = "FOLLOW";
                $follower_activity->save();
                break;
           
            case config('constants.USER_FOLLOW_REQUESTED'):
            	 $activity = new RequestActivity();
                 $activity->follower_id = $followerid;
                 $activity->user_id = $userid;
                 $activity->type = "FOLLOW";
                 $activity->save();
              
                $body =  "@".$username->username. " has requested to following you.";
                $data = ["user_id" => $userid];
                break;

            case config('constants.USER_FOLLOW_APPROVED'):
              
                $body =  "@".$username->username. " has accepted your following request.";
                $data = ["user_id" => $userid];
                $follower_activity->TYPE = "ACEEPTED";
                $follower_activity->save();
                break;    
        }
	$notifyUser = UserProfile::where('user_id',$userid)->first(['fcm_registration_id']);
        PushNotificationHelper::send($notifyUser->fcm_registration_id, $title, $body, $data);
    }

   
}
