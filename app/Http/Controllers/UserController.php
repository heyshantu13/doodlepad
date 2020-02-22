<?php


/*
@Author: Shantanu K.
@email:heyshantu13@gmail.com
@git: heyshantu

*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\User;
use App\UserProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use App\AwsSpace;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

use Aws\S3\S3Client as AWS;



class UserController extends Controller
{
  
  

	public function search(Request $request){
		  $user = Auth::user();
      $profile = User::where('id', $user->id)->firstOrFail();

    //   $countsQuery = [
    //     'followers as is_following' => function ($query) use ($profile) {
    //         $query->where('followables.user_profile_id', $profile->id);
    //     },
    //     'following as following_count',
    //     'followers as followers_count'
    // ];


        $request->validate([
          'q' =>'required|string'
        ]);
       $search = $request->q;

      $user = User::with('userprofiles')->get();
      $profiles = User::select(['id','fullname','username'])
      ->where('username', 'like', '%' . $search . '%')
      ->orWhere('fullname', 'like','%'.$search.'%')
      ->with('userprofiles')
      ->paginate(10);
      try{
        return response()->json($profiles,200);
      }
      catch(\Exception $ex) {

        return response()->json(null,501);
      }
      



	}

	public function updateProfile(Request $request){

     $profile = UserProfile::where('user_id', Auth::user()->id)->firstOrFail();
     $url = "http://api.doodlepad.in/"; //sample url

		$request->validate([
            
             'profile_picture_url'=>'required|image|mimes:jpeg,png,jpg,gif|max:8096',
             'bio' => 'required|string',
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
          return response()->json(null,408);
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

    public function getUser($id){

      // $request->validate([
            
      //        'id'=>'required|string',
      //   ]);

     
      $profile = User::where('id',$id)->first(['id','fullname','username','is_verified','active']);
      $profileDeatils = UserProfile::where('user_id',$id)->firstOrFail(['user_id','profile_picture_url','date_of_birth','is_private','gender','fcm_registration_id','bio']);
      $collection = collect($profile);
      if($collection){
       return response()->json($collection->merge($profileDeatils), 200);
      }
      else{
       return response()->json(null, 404);
      }

    }


      public function getinfo(){

          $imageName = rand(1111,9999).time().'.'.request()->profile_picture_url->getClientOriginalExtension();
          request()->file('profile_picture_url');
          $dofiles = Aws::putObject([
     'Bucket' => 'doodlepadin',
     'Key'    => 'file.ext',
     'Body'   => request()->file('profile_picture_url'),
     'ACL'    => 'private'
]);

          return $dofiles;
      }


      public function follow(UserProfile $userProfile)
      {
          $success = 0; // no action
          $user = Auth::user();
          $currentProfile = UserProfile::where('user_id', $user->id)->first();
          if ($currentProfile->isFollowing($userProfile)) {
              $currentProfile->unfollow($userProfile);
              $success = 1; // unfollow
          } else {
              $currentProfile->follow($userProfile);
              $success = 2; // follow
          }
          return response()->json(array("success" => $success));
      }


      public function refreshFCMid(Request $request){

        $request->validate([
            'fcm_registration_id' => 'required|string'
        ]);

        $uid = Auth::user()->id;
        $profile = UserProfile::where('user_id',$uid)->first();

        if($profile){
          $profile->fcm_registration_id = $request->fcm_registration_id;
        $profile->save();
        return response()->json(['message'=>true],200);
        }
        else{
           return response()->json(['message'=>false],200);
        }
        


      }
  
   




}
