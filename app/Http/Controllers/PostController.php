<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;
use App\UserProfile;
use App\Post;
use App\Http\Requests\CreatePostValidate;



class PostController extends Controller
{
    //


        public function createPost(CreatePostValidate $request){

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
        $post->media_url = $request->media_url;
        $post->save();

        return response()->json(Post::find($post->id),200);
           

        }

        public function myposts(){

        }
}
