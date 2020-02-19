<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\User;
use App\UserProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use App\AwsSpace;

class UserController extends Controller
{
  
  

	public function search(Request $request){
		  $user = Auth::user();
        $profile = User::where('id', $user->id)->firstOrFail();

          $search = $request->q;
        $profiles = User::where('username', 'like', '%' . $search . '%')->paginate();
        return response()->json($profiles,200);


	}

	public function updateProfile(Request $request){

     $profile = UserProfile::where('user_id', Auth::user()->id)->firstOrFail();
     $url = "http://api.doodlepad.in/"; //sample url

		$request->validate([
            
             'profile_picture_url'=>'required|image|mimes:jpeg,png,jpg,gif|max:8096',
             'bio' => 'required|string',
             'gender'=>'required',
        ]);

       

        try {
          $imageName = rand(1111,9999).time().'.'.request()->profile_picture_url->getClientOriginalExtension();
          request()->file('profile_picture_url')->move(public_path("/"),$imageName);
         $profile->profile_picture_url = $url.$imageName;
         $profile->save();
         return response()->json([
          'status'=>true,
  'message' => 'Profile Updated Successfully.',
  'profile_picture_url' => $url.$imageName,
], 200);
        }
        catch(\Exception $ex) {
          abort(400);
      }

       



  }
  
  /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
         $userID = Auth::user()->id;
         $profile = User::where('id',$userID)->first(['id','fullname','username','is_verified','active']);
         $profileDeatils = UserProfile::where('user_id',$userID)->firstOrFail(['user_id','profile_picture_url','date_of_birth','is_private','gender','fcm_registration_id','bio']);
         $collection = collect($profile);
         if($collection){
          return response()->json($collection->merge($profileDeatils), 200);
         }
         else{
          return response()->json(['status'=>false], 500);
         }
      
    }


      public function getinfo(){
        $dospace = new AwsSpace();
       return $dospace->getSpaceInfo();
      }

   



}
