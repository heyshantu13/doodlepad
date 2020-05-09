<?php

namespace App\Http\Controllers;

use App\User;
use App\UserProfile;
use App\BioLike;

class BioLikesController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(User $id)
    {
        $user_id = auth()->user()->id;
        $profile_id = UserProfile::where('user_id',$id->id)->first(['id']);
        $bioliked = BioLike::where('user_id',$user_id)
        ->where('profile_id',$profile_id->id)
        ->first(['id']);

        if($bioliked)
        {
           
            $bioliked->delete();
            return response()->json(-1,200);
           
        }
        $bioliked = new BioLike();
        $bioliked->profile_id = $profile_id->id;
        $bioliked->user_id = $user_id;
        $bioliked->save();
        return response()->json(1,200);
       
    }
}
