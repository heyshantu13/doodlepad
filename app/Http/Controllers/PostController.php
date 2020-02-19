<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;
use App\UserProfile;
use App\Post;
use Illuminate\Validation\Rule;


class PostController extends Controller
{
    //


        public function createPost(Request $request){

            if(request()->type == "TEXT"){
                $userID = Auth::user()->id;
                $request->validate([
                    'type' => 'required',
                    'text'=> 'required|string',
                    'alignment'=>'required',
                    'color'=>'required|min:1|max:1',
                    'caption'=>'required|string'
               ]);     
            }
           

        }

        public function myposts(){

        }
}
