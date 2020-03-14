<?php
/**
 * Created by Shantanu K.
 */

namespace App\Helpers;

use App\Post;
use App\UserProfile;
use App\CommentActivity;
use App\PostActivity;

class PostHelper
{
    static function createPostActivity($profile, $postId, $type)
    {
        $activity = new PostActivity();
        $activity->user_profile_id = $profile->id;
        $activity->post_id = $postId;
        $activity->type = $type;
        $activity->save();

	if(Post::find($postId)->user_profile_id == $profile->id) { return 1; }

        $title = "New Activity on your post";
        $body = null;
        $data = array();
        switch($type) {
            case config('constants.POST_ACTIVITY_LIKE'):
                
                $body = "Someone liked your post";
                $data = ["post_id" => $postId];
                break;
           
            case config('constants.POST_ACTIVITY_COMMENT'):
                if(!$profile->notification_on_comment) {
                    return;
                }
                $body = "Someone commented on your post";
                $data = ["post_id" => $postId];
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

        $title = "New Activity on your post";
        $body = "";
        $data = array();
        switch($type) {
            case config('constants.POST_ACTIVITY_LIKE'):
                $body = "Someone liked your comment";
                $data = ["comment_id" => $commentId];
                break;
            case config('constants.COMMENT_ACTIVITY_REPLY'):
                $body = "Someone reply your comment";
                $data = ["comment_id" => $commentId];
                break;
        }
	$notifyUser = UserProfile::find(Comment::find($commentId)->user_profile_id)->first();
        PushNotificationHelper::send($notifyUser->fcm_registration_id, $title, $body, $data);
    }
}
