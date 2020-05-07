<?php

/*  Author:  Shantanu K
    Git: heyshantu13
    Description:  Authentication Management Controller
*/



namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\PostHelper;
use App\Http\Requests\CreateCommentRequest;
use App\Comment;
use App\CommentActivity;
use App\CommentReply;
use App\Post;
use App\UserProfile;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CommentController extends Controller
{
      public function index($post) {
           $user = Auth::user();
           $profile = UserProfile::where('user_id', $user->id)->firstOrFail();
        $profile = UserProfile::where('user_id', $user->id)->first(['id','profile_picture_url','user_id']);


        $pid = Post::where('id',$post)->first(['id']);
        if(!$pid){ return response()->json(null,404);}
        $cid = Comment::where('post_id',$post)->first(['id']);
        if(!$cid){ return response()->json("no comments",404);}

        $comments = 
            Comment::select('users.id as user_id','users.username','up.profile_picture_url','comments.id as cid','comments.type','comments.post_id','comments.created_at','comments.media_url as comment_media_url')
        ->join('user_profiles as up','up.id','=','comments.user_profile_id')
        ->join('posts','posts.id','=','comments.post_id')
        ->join('users','users.id','=','up.user_id')
        ->where('comments.post_id',$post)
            ->orderByRaw('FIELD (comments.user_profile_id, ' .$profile->id. ') ASC')
        ->orderBy('comments.created_at','DESC');

          $countsQuery = [
           'comment_activities as like_count' => function ($query) use($cid) {
                 $query->where('comment_id', $cid->id)
               ->where('type', 'like');
           },

            'comment_activities as reply_count' => function ($query) use($cid) {

            $query->where('comment_id', $cid->id)
               ->where('type', 'reply');
           },

       ];

             return response()->json($comments->withCount($countsQuery)->paginate(config('constants.paginate_per_page')), 200);
    }

    public function like(Comment $comment) {
        $status = $this->likeDislikeComment($comment, config('constants.COMMENT_ACTIVITY_LIKE'));
        return response()->json(array("id" => $comment->id, "status" => $status), 200);
    }

  

     public function store(Post $post, CreateCommentRequest $request) {
        $user = Auth::user();
        $profile = UserProfile::where('user_id', $user->id)->first();
        $name = "";
            if(request()->hasFile('media_url')){
                     $file = request()->file('media_url');
            $name=time().$file->getClientOriginalName();
            $filePath = 'comments/' .rand(11111,99999). $name;
              $strg = Storage::disk('s3')->put($filePath, file_get_contents($file),'public');
            }

        $comment = new Comment();
        $comment->text = $request->text;
        $comment->type = $request->type;
        $comment->post_id = $post->id;
        $comment->media_url = (request()->hasFile('media_url')) ?   env('AWS_URL')."/".$filePath : null;
        $comment->user_profile_id = $profile->id;
        $comment->save();

    if($post->user_profile_id == $profile->id) { return 1; }
    
        PostHelper::createPostActivity($profile, $post->id, config('constants.POST_ACTIVITY_COMMENT'));
    
    return response()->json(Comment::find($comment->id),200);
        
    }


    public function destroy(Comment $id) {
        $deleted = $id->delete();
        $status = $deleted ? 200 : 400;
        return response()->json(null, $status);
    }

    private function likeDislikeComment($comment, $type)
    {
        $user = Auth::user();
        $profile = UserProfile::where('user_id', $user->id)->firstOrFail();

        $previousActivity = CommentActivity::where('user_profile_id', $profile->id)
            ->where('comment_id', $comment->id)
            ->whereIn('type', array(config('constants.COMMENT_ACTIVITY_LIKE')))
            ->first();

        if ($previousActivity) {
            $previousActivity->delete();

            if ($previousActivity->type == $type) {
                return -1;
            }
        }
	
#	if($comment->user_profile_id->user_profile_id == $profile->id) { return 1; }

        PostHelper::createCommentActivity($profile, $comment->id, $type);
        return 1;
    }

    public function getReplies(Comment $comment_id){


            if($comment_id){

                $user = Auth::user();
             $profile = UserProfile::where('user_id', $user->id)->first(['id','profile_picture_url','user_id']);

             $replies = 
             CommentReply::select('users.id as uid','users.username','user_profiles.id as upid','user_profiles.profile_picture_url','comments.id','comment_replies.id','comment_replies.type','comment_replies.text','comment_replies.media_url','comment_replies.created_at')
             ->join('comments','comments.id','=','comment_replies.comment_id')
             ->join('user_profiles','user_profiles.id','=','comment_replies.user_profile_id')
             ->join('users','users.id','=','user_profiles.user_id')
             ->orderBy('comment_replies.created_at','DESC')
             ->where('comment_replies.comment_id',$comment_id->id)
             ->paginate(5);

                
             return response()->json([$replies],200);

            }
            return response()->json(null,200);
    }


    public function storeReplies(Comment $comment_id,CreateCommentRequest $request){


        $user = Auth::user();
        $profile = UserProfile::where('user_id', $user->id)->first(['id']);
        $name = "";
            if(request()->hasFile('media_url')){
                     $file = request()->file('media_url');
            $name=time().$file->getClientOriginalName();
            $filePath = 'comment_replies/' .rand(11111,99999). $name;
              $strg = Storage::disk('s3')->put($filePath, file_get_contents($file),'public');
            }
        
            $comment_replies = new CommentReply();
            $comment_replies->text = $request->text;
            $comment_replies->type = $request->type;
            $comment_replies->comment_id = $comment_id->id;
            $comment_replies->media_url = (request()->hasFile('media_url')) ?   env('AWS_URL')."/".$filePath : null;
            $comment_replies->user_profile_id = $profile->id;
            $comment_replies->save();   

            if($comment_replies->user_profile_id == $profile->id) { 
                return response()->json(CommentReply::find($comment_replies->id),200);

             }
             PostHelper::createPostActivity($profile, $comment_replies->id, config('constants.COMMENT_ACTIVITY_REPLY'));
            return response()->json(CommentReply::find($comment_replies->id),200);


    }


    // Delete Replies

    public function deleteReplies(Comment $comment_id,CommentReply $reply_id){

        
        $deleted = $reply_id->delete();
        $status = $deleted ? 200 : 400;
        return response()->json(null, $status);

    }
}
