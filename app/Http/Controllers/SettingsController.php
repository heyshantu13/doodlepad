<?php


/*

	@Author : Shantan Kulkarni
	@Github: @heyshantu13
	@Description : Users Setting Controller To Update User Privacy Settings

*/


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\UserProfile;
use Auth;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    //

    protected $old_password;
    protected $new_password;


	/*
		 Get User Current Privacy Status 0 = Public 1 = Private
	*/


    public function getPrivacy(){
    	$status = UserProfile::where('user_id',Auth::user()->id)->first(['is_private']);
    	if($status){
    		/*return dd($status->is_private);*/
    		return response()->json(['status'=>true,'privacy'=>$status]);
    	}
    	return response()->json(['status'=>false,'privacy'=>null]);
    	
    }


    /*
		 Update  User Current Privacy Status 0 = Public 1 = Private
	*/

    public function setPrivacy(){
    	$status = UserProfile::where('user_id',Auth::user()->id)->first(['is_private','id']);

    	if($status->is_private == 0 ){
    		$updateprivacy = UserProfile::find($status->id);
    		$updateprivacy->is_private = 1;
    		$updateprivacy->save();
    		return response()->json(['status'=>true,'message'=>'updated','privacy'=>1],200);

    	}
    	else if($status->is_private == 1)
    	{
    		$updateprivacy = UserProfile::find($status->id);
    		$updateprivacy->is_private = 0;
    		$updateprivacy->save();
    		return response()->json(['status'=>true,'message'=>'updated','privacy'=>0],200);
    	}
    }


    public function getUsername(){
    	return response(['status'=>true,'username'=>Auth::user()->username]);
    }

    public function setUsername(Request $request){
    	$request->validate([
    		 'username' => 'required|string|unique:users|alpha_dash|max:16'
    	]);

    	$user = User::find(Auth::user()->id)->first(['username']);
    	$user->username = $request->username;
    	$user->save();
    	return response(['status'=>true,'username'=>$request->username]);
    }

    /*

		Set New Password
		
    */

		public function setNewPassword(Request $request){

			 $request->validate([
             'current_password' => ['required', new MatchOldPassword],
            'new_password' => 'required|string|min:8|max:14',
            'new_confirm_password' => ['same:new_password'],
            
        ]);        

		User::find(auth()->user()->id)->update(['password'=> Hash::make($request->new_password)]);

		return response(['status'=>true],200);




         

}
