<?php

/**
* AUthor: Shantanu Kulkarni
 */


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\PostHelper;
use App\Http\Requests\CreateCommentRequest;
use App\Comment;
use App\CommentActivity;
use App\Post;
use App\UserProfile;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
      public function index($post) {
        $user = Auth::user();
        $profile = UserProfile::where('user_id', $user->id)->firstOrFail();

        $countsQuery = [
            'comment_activities as like_count' => function ($query) {
                $query->where('type', config('constants.COMMENT_ACTIVITY_LIKE'));
            },
        
            'comment_activities as liked' => function ($query) use ($profile) {
                $query->where('user_profile_id', $profile->id)
                    ->where('type', config('constants.COMMENT_ACTIVITY_LIKE'));
            },
            
        ];

        $comments = Comment::where('post_id', $post)->orderBy('created_at', 'desc')
        ->withCount($countsQuery)->paginate(config('constants.paginate_per_page'));
        return response()->json($comments,200);
    }

    public function like(Comment $comment) {
        $status = $this->likeDislikeComment($comment, config('constants.COMMENT_ACTIVITY_LIKE'));
        return response()->json(array("id" => $comment->id, "status" => $status), 200);
    }

  

    public function store(Post $post, CreateCommentRequest $request) {
        $user = Auth::user();
        $profile = UserProfile::where('user_id', $user->id)->first();
        $comment = new Comment();
        $comment->text = $request->text;
        $comment->type = $request->type;
        $comment->post_id = $post->id;
        $comment->user_profile_id = $profile->id;
        $comment->save();

 	if($post->user_profile_id == $profile->id) { return 1; }
	
        PostHelper::createPostActivity($profile, $post->id, config('constants.POST_ACTIVITY_COMMENT'));

        return response()->json(Comment::find($comment->id),200);
    }

    public function destroy(Comment $comment) {
        $deleted = $comment->delete();
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
}
