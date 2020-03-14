<?php
/**
 * Created by Shantanu K.
 */

namespace App\Helpers;

use App\Post;
use App\UserProfile;
use App\User;
use App\FollowerActivity;

class FollowerHelper
{

	 static function createFollowActivity($follower_id, $user_id)
    {
        $activity = new PostActivity();
        $activity->user_profile_id = $profile->id;
        $activity->post_id = $postId;
        $activity->type = $type;
        $activity->save();
        $username = User::where('id',$profile->user_id)->first(['username']);
       
     

	if(Post::find($postId)->user_profile_id == $profile->id) { return 1; }

        $title = "New Activity on your post";
        $body = null;
        $data = array();
        switch($type) {
            case config('constants.POST_ACTIVITY_LIKE'):
                
                $body =  "@".$username->username." liked your post";
                $data = ["post_id" => $postId];
                break;
           
            case config('constants.POST_ACTIVITY_COMMENT'):
                if(!$profile->notification_on_comment) {
                    return;
                }
                $body =  "@".$username->username. "commented on your post";
                $data = ["post_id" => $postId];
                break;
        }
	$notifyUser = UserProfile::where('id',Post::find($postId)->user_profile_id)->first(['fcm_registration_id']);
        PushNotificationHelper::send($notifyUser->fcm_registration_id, $title, $body, $data);
    }

}
