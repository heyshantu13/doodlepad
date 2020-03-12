<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;
use App\UserProfile;
use App\Post;
use App\Http\Requests\CreatePostValidate;
use Illuminate\Support\Facades\Storage;
use App\Helpers\PostHelper;
use App\PostActivity;



class PostController extends Controller
{


   public function index(Request $request)
    {
        $user = Auth::user();
        $profile = UserProfile::where('user_id', $user->id)->firstOrFail();

        $countsQuery = [
            'post_activities as like_count' => function ($query) {
                $query->where('type', config('constants.POST_ACTIVITY_LIKE'));
            },
           
            'post_activities as comment_count' => function ($query) {
                $query->where('type', config('constants.POST_ACTIVITY_COMMENT'));
            },
            'post_activities as liked' => function ($query) use ($profile) {
                $query->where('user_profile_id', $profile->id)
                    ->where('type', config('constants.POST_ACTIVITY_LIKE'));
            },
           
            'post_activities as commented' => function ($query) use ($profile) {
                $query->where('user_profile_id', $profile->id)
                    ->where('type', config('constants.POST_ACTIVITY_COMMENT'));
            }
        ];
        if ($request->treding === "1") {
            $posts = Post::orderBy('created_at', 'desc');
        } else {
            // $following = array_merge($profile->followings()->all(), [$profile->id]);
          $following =  User::find($user->id)->followings()->pluck('user_id');
          
            $posts = Post::whereIn('user_id', $following)->orWhere('user_id',$user->id)->orderBy('created_at', 'desc');
        }
        if ($request->type) {
            $posts = $posts->where('type', $request->type)->orderBy('created_at', 'desc');
        }
        if ($request->user_profile_id) {
            $posts = $posts->where('user_profile_id', $request->user_profile_id)->orderBy('created_at', 'desc');
        }
        $posts = $posts->withCount($countsQuery)->paginate(config('constants.paginate_per_page'));
        return response()->json($posts,200);




    }
   


        public function createPost(CreatePostValidate $request){
            $name = "";
            if(request()->hasFile('media_url')){
                     $file = request()->file('media_url');
            $name=time().$file->getClientOriginalName();
            $filePath = 'posts/' . $name;
              $strg = Storage::disk('s3')->put($filePath, file_get_contents($file),'public');
            }
       
               
          $user = Auth::user();
        $profile = UserProfile::where('user_id', $user->id)->first();
        $post = new Post();
        $post->text = $request->text;
        $post->type = $request->type;
        $post->alignment = $request->alignment;
        $post->user_profile_id = $profile->id;
        $post->caption = $request->caption;
        $post->text_location = $request->text_location;
        $post->longitude = $request->longitude;
        $post->latitude = $request->latitude;
        $post->user_id =  $user->id;

        $post->media_url = (request()->hasFile('media_url')) ?  env('AWS_URL')."/".$filePath : NULL;
        $post->filename = (request()->hasFile('media_url')) ?  $name : "";
        $post->save();
          return response()->json(['status'=>true,'post'=>Post::find($post->id)],200);
        
      
           

        }


       
          public function myPosts(){

            $user = Auth::user();
         $profile = UserProfile::where('user_id', $user->id)->firstOrFail();
 
 
 
         $countsQuery = [
           'post_activities as like_count' => function ($query) {
               $query->where('type', config('constants.POST_ACTIVITY_LIKE'));
           },
           
           'post_activities as comment_count' => function ($query) {
               $query->where('type', config('constants.POST_ACTIVITY_COMMENT'));
           },
           'post_activities as liked' => function ($query) use ($profile) {
               $query->where('user_profile_id', $profile->id)
                   ->where('type', config('constants.POST_ACTIVITY_LIKE'));
           },
          
           'post_activities as commented' => function ($query) use ($profile) {
               $query->where('user_profile_id', $profile->id)
                   ->where('type', config('constants.POST_ACTIVITY_COMMENT'));
           }
       ];
 
       $posts = Post::where('user_profile_id', $profile->id);
 
 
       $posts = $posts->orderBy('created_at', 'desc')->withCount($countsQuery)->paginate(config('constants.paginate_per_page'));
       return response()->json($posts,200);
 
        
         }

          public function like(Post $post)
    {
        $status = $this->likeDislikePost($post, config('constants.POST_ACTIVITY_LIKE'));
        return response()->json(array("id" => $post->id, "status" => $status), 200);
    }



          private function likeDislikePost($post, $type)
    {
        $user = Auth::user();
        $profile = UserProfile::where('user_id', $user->id)->firstOrFail();

        $previousActivity = PostActivity::where('user_profile_id', $profile->id)
            ->where('post_id', $post->id)
            ->whereIn('type', array(config('constants.POST_ACTIVITY_LIKE')))
            ->first();

        if ($previousActivity) {
            $previousActivity->delete();

            if ($previousActivity->type == $type) {
                return -1; // unlike or undislike
            }
        }

        PostHelper::createPostActivity($profile, $post->id, $type);
        return 1; // like or dislike
    }


     public function show(Post $post)
    {
        $user = Auth::user();
        $profile = UserProfile::where('user_id', $user->id)->firstOrFail();

        $countsQuery = [
            'post_activities as like_count' => function ($query) {
                $query->where('type', config('constants.POST_ACTIVITY_LIKE'));
            },
          
            'post_activities as comment_count' => function ($query) {
                $query->where('type', config('constants.POST_ACTIVITY_COMMENT'));
            },
            'post_activities as liked' => function ($query) use ($profile) {
                $query->where('user_profile_id', $profile->id)
                    ->where('type', config('constants.POST_ACTIVITY_LIKE'));
            },
          
            'post_activities as commented' => function ($query) use ($profile) {
                $query->where('user_profile_id', $profile->id)
                    ->where('type', config('constants.POST_ACTIVITY_COMMENT'));
            }
        ];
        return response()->json(Post::where('id', $post->id)->withCount($countsQuery)->first());
    }




       
      }