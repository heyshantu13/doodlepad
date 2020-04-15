<?php

    /*
                   Create Post Activities

                    Author: Shantanu Kulkarni

                    Github: heyshantu13

            */

namespace App\Helpers;

use App\Post;
use App\UserProfile;
use App\CommentActivity;
use App\PostActivity;
use App\User;

class PostHelper
{
    static function createPostActivity($profile, $postId, $type,$comment=null)
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
                
                $body =  "@".$username->username." liked your doodlepad post";
                $data = ["post_id" => $postId,"image" => $image];
                break;
           
            case config('constants.POST_ACTIVITY_COMMENT'):

            /*
                    Check If User Mentioned Any User Or Not

                    Author: Shantanu Kulkarni

                    Github: heyshantu13

            */
                $isMentioned = preg_match_all('/@(\w+)|\s+([(\w+)\s|.|,|!|?]+)/', $comment, $result, PREG_PATTERN_ORDER);
                 if($isMentioned)
                    {
                        for ($i = 0; $i < count($result[0]); $i++) {
                        $mention[$i]= $result[1][$i];
                                }
                        for ($j = 0; $j< $i; $j++){
                             $body =  "@".$username->username." mentioned you in comment";
                                $data = ["post_id" => $postId,"image" => $image];

                        $user = User::where('username',$mention[$j])->first(['id']);
                        if($user){
                             $fcm_token = UserProfile::where('user_id',$user->id)->first(['fcm_registration_id']);
                            $activity = new PostActivity();
                             $activity->user_profile_id = $profile->id;
                            $activity->post_id = $postId;
                            $activity->type = "MENTIONED";
                            $activity->save();
                            PushNotificationHelper::send($fcm_token->fcm_registration_id, $title, $body, $data);
                        }
                       

                          
          
                                }

                    }
              
                $body =  "@".$username->username. " commented on your doodlepad post";
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
                $body =  "@".$username->username."reply to your comment";
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
         $body = "Your Post Will Disappear in 1 hour";
           $data = ["post_id" => $postId];
          
          if(!$isNotified){
                $activity = new PostActivity();
         $activity->user_profile_id = $profileid;
        $activity->post_id = $postId;
        $activity->type = "PINNED";
        $activity->save();
        $notifyUser = UserProfile::where('id',$profileid)->first(['fcm_registration_id']);
        PushNotificationHelper::send($notifyUser->fcm_registration_id, $title, $body, $data);
          }

    }

}

