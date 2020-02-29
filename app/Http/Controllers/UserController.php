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
use App\Follower;

use Aws\S3\S3Client;




class UserController extends Controller
{


  
  
  public function isFollowing(int $id){
     $user = Auth::user()->id;

    $isFollowing = DB::table('followers')->where('follower_id',$user)->where('user_id',$id)->exists();
return $isFollowing;
    
}

  public function checkFollowing(int $id){
     $user = Auth::user()->id;

    $isFollowing = DB::table('followers')->where('follower_id',$user)->where('user_id',$id)->exists();
    if($isFollowing){
       return response()->json([
          'status'=>true,
          'message'=>'Following',
        ],200);
    }
    else if(!$isFollowing){
      return response()->json([
          'status'=>true,
          'message'=>'Follow',
        ],200);
    }
    else{
       return response()->json([
          'status'=>true,
          'message'=>'Requested',
        ],200);
    }
    
}

   public function follow(int $id)
{
          $user_id = Auth::user()->id;
          $profile = User::where('id', $id)->firstOrFail();
          if($profile)
          {
             $isFollowingOrNot = Self::isFollowing($id);
             //  @return already following message

             if($isFollowingOrNot){
               $isFollowing = DB::table('followers')->where('follower_id',$user_id)->where('user_id',$id)->delete();
              return response()->json([
          'status'=>true,
          'message'=>'Unfollow Success',
        ],200);
             }
             else{
              $profile->followers()->attach(auth()->user()->id);
              return response()->json([
          'status'=>true,
          'message'=>'Follow Success',
        ],200);
             }

           }


           //  If User Not Found

           else{
              return response()->json([
          'status'=>false,
          'message'=>'Invalid Request',
        ],408);
           }

    
}

 public function following()
{
          $user_id = Auth::user()->id;
          // $profile = User::with('userprofiles')->where('id', $user_id)->firstOrFail();
          $followings = User::find(54)->followings()->with('userprofiles')->paginate(10);
        return response()->json([
          'message'=>true,
          'followings'=>$followings,
        ],200);

      }

          public function followers()
{
          $user_id = Auth::user()->id;
          $profile = User::where('id', $user_id)->firstOrFail();
      $followers = $profile->followers;
        return response()->json([
          'message'=>true,
          'followers'=>$followers,

        ],200);
    
}



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
             'bio' => 'string',
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
