<?php
/**
 * Created by Shantanu K.
 */

namespace App\Helpers;

use App\Post;
use App\UserProfile;
use App\CommentActivity;
use App\PostActivity;
use App\User;

class PostHelper
{
    static function createPostActivity($profile, $postId, $type)
    {
        $activity = new PostActivity();
        $activity->user_profile_id = $profile->id;
        $activity->post_id = $postId;
        $activity->type = $type;
        $activity->save();
        $username = User::where('id',$profile->user_id)->first(['username']);
        $image = $profile->profile_picture_url;
       
     

	if(Post::find($postId)->user_profile_id == $profile->id) { return 1; }

        $title = "Doodlepad";
        $body = null;
        $data = array();
        switch($type) {
            case config('constants.POST_ACTIVITY_LIKE'):
                
                $body =  "@".$username->username." liked your post";
                $data = ["post_id" => $postId,"image" => $image];
                break;
           
            case config('constants.POST_ACTIVITY_COMMENT'):
                if(!$profile->notification_on_comment) {
                    return;
                }
                $body =  "@".$username->username. "commented on your post";
                $data = ["post_id" => $postId,
                "image" => $image
            ];
                break;
        }
	$notifyUser = UserProfile::where('id',Post::find($postId)->user_profile_id)->first(['fcm_registration_id']);
        PushNotificationHelper::send($notifyUser->fcm_registration_id, $title, $body, $data);
    }

    static function createCommentActivity($profile, $commentId, $type)
    {
        $activity = new CommentActivity();
        $activity->user_profile_id = $profile->id;
        $activity->comment_id = $commentId;
        $activity->type = $type;
        $activity->save();

	if(Comment::find($commentId)->user_profile_id->id == $profile->id) { return 1; }

        $title = "Doodlepad";
        $body = "";
        $data = array();
        switch($type) {
            case config('constants.POST_ACTIVITY_LIKE'):
                $body =  "@".$username->username." liked your comment";
                $data = ["comment_id" => $commentId,"image" => $image];
                break;
            case config('constants.COMMENT_ACTIVITY_REPLY'):
                $body =  "@".$username->username."reply your comment";
                $data = ["comment_id" => $commentId,"image" => $image];
                break;
        }
	$notifyUser = UserProfile::find(Comment::find($commentId)->user_profile_id)->first();
        PushNotificationHelper::send($notifyUser->fcm_registration_id, $title, $body, $data);
    }

    static function createNotifyActivity($profileid, $postId)
    {
        $isNotified = PostActivity::where('post_id',$postId)->where('user_profile_id',$profileid)->first(['id']);
         $title = "Doodlepad";
         $body = "Your Post Will Disappear in few hour";
           $data = ["post_id" => $postId];
          
          if(!$isNotified){
                $activity = new PostActivity();
         $activity->user_profile_id = $profileid;
        $activity->post_id = $postId;
        $activity->type = "PINNED";
        $activity->save();
          }

         $notifyUser = UserProfile::where('id',$profileid)->first(['fcm_registration_id']);
        PushNotificationHelper::send($notifyUser->fcm_registration_id, $title, $body, $data);


    }
}
