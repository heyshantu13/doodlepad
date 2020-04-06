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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use App\Follower;
use App\Helpers\FollowerHelper;
use App\RequestActivity;
use App\Post;
// use Aws\S3\S3Client;




class UserController extends Controller
{


  

  public function checkFollowing(int $id){
     $user = Auth::user()->id;

    $isFollowing = Follower::where('follower_id',$user)->where('user_id',$id)->first();
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



 public function following()
{
          $user_id = Auth::user()->id;
          // $profile = User::with('userprofiles')->where('id', $user_id)->firstOrFail();
          $followings = User::find($user_id)->followings()->with('userprofiles')->paginate(10);
        return response()->json([
          'message'=>true,
          'followings'=>$followings,
        ],200);

      }

          public function followers()
{
          $user_id = Auth::user()->id;
          $followers = User::find($user_id)->followers()->with('userprofiles')->paginate(10);
        return response()->json([
          'message'=>true,
          'followers'=>$followers,

        ],200);
    
}



	public function search(Request $request){
		 
        $request->validate([
          'q' =>'required|string'
        ]);
         $user = Auth::user();
      $profile = User::where('id', $user->id)->firstOrFail();
       $search = $request->q;

        $countsQuery = [
            'followers as is_following' => function ($query) use ($profile) {
                $query->where('followers.user_id', $profile->id);
            },
            'followings as following_count',
            'followers as followers_count'
        ];


          $profiles = User::select(['id','fullname','username'])
      ->where('username', 'like', '%' . $search . '%')
      ->orWhere('fullname', 'like','%'.$search.'%')
      ->with('userprofiles')
      ->withCount($countsQuery)->paginate(config('constants.paginate_per_page'));

        return response()->json($profiles,200);


  



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
        $info = array(
            "follower_counts" => Follower::where('user_id',$userID)->count(),
            "post_counts" => $profile->posts()->count(),
          );

         $profileDeatils = UserProfile::where('user_id',$userID)->firstOrFail(['user_id','profile_picture_url','is_private','bio']);
         $collection = collect($profile);
         if($collection){
          return response()->json($collection->merge($profileDeatils)->merge($info), 200);
         }
         else{
          return response()->json(['status'=>false], 500);
         }
         
      
    }

    public function getUser($id){

      // $request->validate([
            
      //        'id'=>'required|string',
      //   ]);

      $info = null;
     
      $profile = User::where('id',$id)->firstOrFail(['id','fullname','username','is_verified','active']);
      $profileDeatils = UserProfile::where('user_id',$id)->firstOrFail(['profile_picture_url','is_private','bio']);
      if($profile){
        $isFollowing = Follower::where('follower_id',Auth::user()->id)->where('user_id',$id)->first();
        if($isFollowing)
        {
          $isFollowing = "1" ;//Following;
        }
        if(!$isFollowing){
          $isrequestedOrNot = RequestActivity::where('follower_id',Auth::user()->id)
          ->where('user_id',$id)
          ->first();
          if($isrequestedOrNot){
             $isFollowing= 2 ;//Requested;
           }
           else{
              $isFollowing= 0; //Not Following;
           }
        }

        $info = array(
            "follower_counts" => Follower::where('user_id',$id)->count(),
            "post_counts" => $profile->posts()->count(),
            "following_status"=> $isFollowing,
            "current_user"=> (Auth::user()->id == $id) ? 1:0,
          );
      }

      $collection = collect($profile);
      if($collection){
       return response()->json($collection->merge($profileDeatils)->merge($info), 200);
      }
      else{
       return response()->json(null, 404);
      }

    }

    public function userPosts($id){
//       $userID = Auth::user()->id;
//       $isFollowing = Follower::where('follower_id',$userID)
//        ->where('user_id',$id)
//           ->firstOrFail();
//        if($isFollowing){
//           $message = true;
//          $posts = Post::where('user_id',$id)->paginate(config('constants.paginate_per_page'));
//        }
//        else{
//         $isPrivate = UserProfile::where('user_id',$id)->first(['is_private']);
//         if(!$isFollowing && $isPrivate->is_private){
//            $message = false;
//            $posts = array("message"=>"This Account is Private.");
//         }
//         else{
//            $message = true;
//          $posts = Post::where('user_id',$id)->paginate(config('constants.paginate_per_page'));
//         }

//        }   
           $posts = Post::where('user_id',$id)->paginate(config('constants.paginate_per_page'));
        return response()->json(["status" => true,"posts"=>$posts], 200);

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

      public function follow(User $user){
         $message = $this->FollowUnfollowUser($user->id);
        return response()->json(["status" => true,"id" => $user->id, "message"=>$message], 200);
      }


        private function FollowUnfollowUser(int $id)
    {
        $user = Auth::user();
         $profile = User::where('id', $id)->firstOrFail();
        $isFollowingOrNot = Follower::where('follower_id',$user->id)->where('user_id',$id)->first();
        $is_private = UserProfile::where('user_id',$id)->first(['is_private']);

        if ($isFollowingOrNot) {
            $isFollowingOrNot->delete();
             return "Unfollow Success";

        }

        //  if(!$isFollowingOrNot && $is_private){
        //   $isrequestedOrNot = RequestActivity::where('follower_id',$user->id)
        //   ->where('user_id',$id)
        //   ->first();
        //   if($isrequestedOrNot){
        //     $isrequestedOrNot->delete();
        //      return "Requested Cancelled";
        //   }
        //    FollowerHelper::FollowerActivity($user->id,$id,"REQUESTED");
        //          return "Requested";
        // }

        
        else{
          // if account is private
          if($is_private->is_private){

// Check If Already Requested Or Not
               $isrequestedOrNot = RequestActivity::where('follower_id',$user->id)
          ->where('user_id',$id)
          ->first();


          if($isrequestedOrNot){
            $isrequestedOrNot->delete();
             return "Request Cancelled";
           }
           else{
            FollowerHelper::FollowerActivity($user->id,$id,"REQUESTED");
                 return "Requested";
           }

          }

// For Public Account
            $profile->followers()->attach(auth()->user()->id);
             

              FollowerHelper::FollowerActivity($user->id,$id,"FOLLOWING");
                 return "Follow Success";

        }
      


       
    }

  
   




}
