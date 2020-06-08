<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\UserProfile;
use App\User;
use App\Follower;
use App\ProfileDoodle;
use DB;
use Illuminate\Support\Facades\Storage;
use App\Helpers\PostHelper;
use App\PostActivity;
use App\RequestActivity;



class DoodleController extends Controller
{

    private $user;
    private $user_profile;
    private $follower;
    private $follower_profile;



    
    public function index(){
 
    }


    public function storeDoodles(Request $request,$id){

		

		//return $by_user_profile;   OK

		$name = "";
		

    	$request->validate([
             'doodle' => 'required|image|mimes:png,jpeg,jpg|max:6096',
		]);

		$by_user_id = Auth::user()->id;
		$user_profile = UserProfile::where('user_id',$id)->first(['id','user_id']);
		//$by_user_profile = UserProfile::where('user_id',$by_user_id)->first(['id','user_id','is_private','profile_picture_url']);
		
		if($user_profile && $id != $by_user_id){

			$file = request()->file('doodle');
			$name=time().$file->getClientOriginalName();
			$filePath = 'posts/' . $name;
			 Storage::disk('s3')->put($filePath, fopen($file, 'r+'),'public');
			 
			$createrequest = new RequestActivity();
			$createrequest->follower_id = $by_user_id;
			$createrequest->user_id = $user_profile->user_id;
			$createrequest->type = "DOODLE";
			$createrequest->media_url = env('AWS_URL')."/".$filePath;
			$saved = $createrequest->save();

			// $newdoodle = new ProfileDoodle();
			// $newdoodle->user_id = $id;
			// $newdoodle->by_user_id = $by_user_id;
			// $newdoodle->media_url = env('AWS_URL')."/".$filePath;
			// $saved = $newdoodle->save();
			if($saved){
			return response()->json(['status'=>true,'message'=>'Profile Doodle Request Sent.'],201);
			}
			return response()->json(['status'=>false,'message'=>'Something Went Wrong'],403);
		}

		return response()->json(['status'=>false,'message'=>'Unable to create doodle'],404);

       




    }

    public function getDoodles($id){    	

		if($id){
			$doodles = ProfileDoodle::select(['by_user_id','media_url','created_at'])
			->where('user_id',$id)->paginate(10);
			if($doodles){
				return response()->json(['status'=>true,'doodles'=>$doodles],200);
			}
			else{
				return response()->json(['status'=>true,'doodles'=>null],200);
			}
		}

		return response()->json(['status'=>false,'doodles'=>null],403);

	}
	
	public function doodleRequests(){

		$user_id = Auth::user()->id;
		
		$activities = RequestActivity::select('request_activities.id as rid', 'request_activities.follower_id','request_activities.user_id','request_activities.media_url','users.username as username','user_profiles.profile_picture_url')
		->join('users','users.id','=','request_activities.follower_id')
		->join('user_profiles','user_profiles.user_id','=','request_activities.follower_id')
		->where('request_activities.user_id',$user_id)
		->where('request_activities.type','DOODLE')
		->paginate(10);

		return response()->json(['status'=>true,'data'=>$activities],200);



	}

	public function acceptdoodle($id){
		$user_id =  Auth::user()->id;
		$request_id = $id;
		$is_valid_request = App\RequestActivity::where('id',$request_id,'user_id',$user_id)->first();

		if($is_valid_request){
			
				$newdoodle = new ProfileDoodle();
			$newdoodle->user_id = $user_id;
			$newdoodle->by_user_id = $is_valid_request->user_id;
			$newdoodle->media_url = $is_valid_request->media_url;
			$saved = $newdoodle->save();
			return response()->json(['status'=>true,'message'=>'doodle request accept']);
		}
		else{
			return response()->json(['status'=>false,'message'=>'something went wrong']);
		}

	}

	public function rejectdoodle(){
		$user_id =  Auth::user()->id;
		$request_id = $id;
		$is_valid_request = App\RequestActivity::where('id',$request_id,'user_id',$user_id)->first();

		if($is_valid_request){
				$is_valid_request->delete();
				return response()->json(['status'=>true,'message'=>'request cancelled.']);
		}

		else{
				return response()->json(['status'=>false,'message'=>'something went wrong.']);
		}
		
	}

}
