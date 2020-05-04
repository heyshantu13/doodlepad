<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Follower;
use App\Profile;
use App\UserProfile;


class UserSuggetions extends Controller
{
    //



    public function index(){

    	 $user = Auth::user();
    $profile = UserProfile::where('user_id',$user->id)->first(['id','profile_picture_url']);

    // $suggetions = 


    }
}
