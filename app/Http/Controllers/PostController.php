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
use App\Comment;
use App\Helpers\LvCount;


class PostController extends Controller
{


/*
    Get all posts on Home Screen
*/

   

   public function index(Request $request)
    {
         $lv_count = new LvCount;
        $user = Auth::user();
        $profile = UserProfile::where('user_id', $user->id)->firstOrFail();

      
          $following =  User::find($user->id)->followings()->pluck('user_id');
          
            $posts = Post::whereIn('user_id', $following)
            ->with('user')
            ->with('userprofile')
            ->orWhere('user_id',$user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(config('constants.paginate_per_page'));


            if($posts){
              foreach ($posts as $key => $value) {
                # code...

              //Check Logged In user Like or Not
               $posts[$key]->liked = PostActivity::where('post_id',$value->id)
               ->where('user_profile_id',$profile->id)
               ->where('type','LIKE')
               ->get()
               ->count();


               // Check Total Number Of Likes

               $totalLikeCounts = PostActivity::where('post_id',$value->id)
               ->where('type','LIKE')
               ->get()
               ->count();

               $posts[$key]->like_count = $lv_count->lv_count($totalLikeCounts);


               // Check Commentef Or Not

                $posts[$key]->commented = PostActivity::where('post_id',$value->id)
               ->where('user_profile_id',$profile->id)
               ->where('type','COMMENT')
               ->get()
               ->count();


               // Check Comment Counts
              
               $totalCommentCounts = Comment::where('post_id',$value->id)
               ->get()
               ->count();


               $posts[$key]->comment_count = $lv_count->lv_count($totalCommentCounts);



               // Check Is Pined

                $posts[$key]->is_pinned = Post::where('id',$value->id)
               ->where('is_pinned','1')
               ->get()
               ->count();

               

               //comments deletable 
                $posts[$key]->comments_deletable = Post::where('id',$value->id)
               ->where('user_profile_id',$profile->id)
               ->get()
               ->count();


               // pinned_counts

                $posts[$key]->pinned_counts = 0;


               

               
              }
            
            }
     
         




        
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

                return -1;  // unlike
         
        }

        else{
          PostHelper::createPostActivity($profile, $post->id, $type);
        return 1; // like
        }
        
      

        
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


    public function likes(Post $id){

      $checkpost = Post::where('id',$id)->first(['id','user_profile_id','user_id']);

    }


       
      }
