<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\UserProfile;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    //

	public function search(Request $request){
		  $user = Auth::user();
        $profile = UserProfile::where('user_id', $user->id)->firstOrFail();

          $search = $request->q;
        $profiles = User::where('username', 'like', '%' . $search . '%')->paginate();
        return response()->json($profiles);


	}


}
