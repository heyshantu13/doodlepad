<?php

/*  Author:  Shantanu K
    Git: heyshantu13
    Description:  Authentication Management Controller
*/



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
use Illuminate\Support\Collection;
use App\SavePost;
use DB;


class PostController extends Controller
{


/*
    Get all posts on Home Screen
*/

   public function index(Request $request)
    {
        $user = Auth::user();
        $profile = UserProfile::where('user_id', $user->id)->firstOrFail();

        $countsQuery = [

          'post_activities as pinned_count' => function ($query) {
                $query->where('type', config('constants.POST_ACTIVITY_PINNED'));
            },

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
            },






        ];

       

        if ($request->treding === "1") {
            $posts = Post::orderBy('created_at', 'desc');
        } else {
      
          $following =  User::find($user->id)->followings()->pluck('user_id');
          
            $posts = Post::whereIn('user_id', $following)
            ->with('user')
            ->with('userprofile')
            ->orWhere('user_id',$user->id)
            // ->inRandomOrder()
            ->orderBy('created_at', 'desc');
        }
        if ($request->type) {
            $posts = $posts->where('type', $request->type)->orderBy('created_at', 'desc');
        }
        if ($request->user_profile_id) {
            $posts = $posts->where('user_profile_id', $request->user_profile_id)
            ->orderBy('created_at', 'desc');
        }

       /* $comment_deletable = [
          'comments_deletabel' => Post::where('user_profile_id',$profile->id)
          ->whereIn('id', $posts->pluck('id'))->count(),
        ];*/

         




        $posts = $posts->withCount($countsQuery)->paginate(config('constants.paginate_per_page'));
        return response()->json($posts,200);




    }
   


        public function createPost(CreatePostValidate $request){


            $name = "";
            if(request()->hasFile('media_url')){
                     $file = request()->file('media_url');
            $name=time().$file->getClientOriginalName();
            $filePath = 'posts/' . $name;
             $strg = Storage::disk('s3')->put($filePath, fopen($file, 'r+'),'public');
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
        $post->color = ($request->color) ? $request->color : 1;

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
 
 
       $posts = $posts->orderBy('created_at', 'desc')
       ->withCount($countsQuery)->paginate(config('constants.paginate_per_page'));
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
        
       /* if($profile->user_id == $post->user_id){
            if ($previousActivity->type == $type) {
                return -1; // unlike or undislike
            }
            else{
                return 1;
            }
        }*/

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
            },

             'post as is_pinned' => function ($query) use ($profile) {
                $query->where('user_profile_id', $profile->id)
                    ->where('is_pinned', 1);
            }
        ];
        return response()->json(Post::where('id', $post->id)->withCount($countsQuery)->first());
    }


      /*
            Delete post only by owner
      */


   public function destroy($id)
    {

      $status = 403;

      $is_post_available = Post::where('id',$id)->where('user_id',Auth::user()->id)->first(['id']);

      if($is_post_available)
      {
        $deleted = $is_post_available->delete();
        $status = ($deleted)?200:400;
      }
      else{
        $status = 404;
      }

       return response()->json(null,$status);

      
       
    }

    public function pinned($id)
    {
       

      $userid = Auth::user()->id;
      $userprofile = UserProfile::where('user_id',$userid)->first();
      $post = Post::where('id',$id)
      ->where('user_profile_id',$userprofile->id)
      ->first();

      if($post)
      {

          if($post->is_pinned)
          {
            $post->is_pinned = 0;
            $post->save();
            $status = 200;
             return response()->json(["status"=>"Post Unpinned Succesfully"],200);
          }
          elseif(!$post->is_pinned){

            $post->is_pinned = 1;
            $post->save();
            $status = 200;
             return response()->json(["status"=>"Post Pinned Succesfully"],200);

          }
          else{
            $status = 400;
             return response()->json(["status"=>"Something Went Wrong"],404);

          }

      }

      else{

        return response()->json(["status"=>"Post Not Found"],404);

      }
        


       
    }


    public function likes($id){

      $isPostAvailable = Post::where('id',$id)->first();

      if($isPostAvailable){
           $likesdata = PostActivity::select('post_activities.*','users.*','user_profiles.*','users.id as user_id')
           ->join('user_profiles','user_profile_id','=','post_activities.user_profile_id')
           ->join('users','user_id','=','user_profiles.user_id')
           ->where('post_activities.type','=','LIKE')
           ->where('post_activities.post_id','=',$id)
            ->paginate(config('constants.paginate_per_page'));
            return response($likesdata,200); 
      }


    }



       
      }
