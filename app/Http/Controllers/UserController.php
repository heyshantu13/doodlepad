<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\User;
use App\UserProfile;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    //

	public function search(Request $request){
		  $user = Auth::user();
        $profile = User::where('id', $user->id)->firstOrFail();

          $search = $request->q;
        $profiles = User::where('username', 'like', '%' . $search . '%')->paginate();
        return response()->json($profiles);


	}

	public function updateProfile(Request $request){

		 $profile = UserProfile::where('user_id', Auth::user()->id)->firstOrFail();

		$request->validate([
            
             'profile_picture_url'=>'required|image|mimes:jpeg,png,jpg,gif|max:8096'
        ]);

        $imageName = rand(1111,9999).time().'.'.request()->profile_picture_url->getClientOriginalExtension();
         request()->file('profile_picture_url')->move(public_path("/"),$imageName);

        $profile->profile_picture_url = $imageName;


        $profile->save();

        return response()->json([
                    'status'=>true,
            'message' => 'Profile Updated Successfully.',
            'profile_picture_url' => 'http://api.doodlepad.in/'.$imageName,
        ], 200);



	}




}
