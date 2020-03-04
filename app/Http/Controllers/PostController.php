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




class PostController extends Controller
{
   


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

        $post->media_url = (request()->hasFile('media_url')) ?  env('AWS_URL')."/".$filePath : NULL;
        $post->filename = (request()->hasFile('media_url')) ?  $name : "";
        $post->save();
          return response()->json(['status'=>true,'post'=>Post::find($post->id)],200);
        
      
           

        }

        public function myPosts(){

            $user = Auth::user();
        $profile = UserProfile::where('user_id', $user->id)->firstOrFail();

        $posts = Post::where('user_profile_id', $profile->id);
            if($posts){
                 return response()->json(['status'=>true,'posts'=>'Not Available'],200);
            }
            else{
                 $posts = $posts->orderBy('created_at', 'desc')->paginate(10);
        return response()->json(['status'=>true,'posts'=>$posts],200);
            }
       
        }


        public function pinPost($id){
             $user = Auth::user();
        $profile = UserProfile::where('user_id', $user->id)->firstOrFail();
        $posts = Post::where('user_profile_id', $profile->id)
        ->where('id',$id)->firstOrFail();
        // if($posts->exists()){
        //    if($posts->is_pinned == 1){
        //     $posts->is_pinned == 0;
        //     $posts->save();
        //    }
        //    else{
        //         $posts->is_pinned == 1;
        //     $posts->save();
        //    }
        // }
         $posts->is_pinned() = 1;
         $posts->save();

         
      

        }
}
