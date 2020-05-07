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



class DoodleController extends Controller
{

    private $user;
    private $user_profile;
    private $follower;
    private $follower_profile;



    
    public function index(){
 
    }


    public function storeDoodles(Request $request,$id){



    	$request->validate([
             'doodle' => 'required|image|mimes:png|max:6096',
        ]);

     

        $user = User::where('id',$id)->first(['id','username']);
        if($user->id == Auth::user()->id){ return 0;}

        if(!$user){ return response()->json(["status"=>false,"doodle_url"=>null],404);}

         $name = "";
            if(request()->hasFile('doodle')){
            	 $file = request()->file('doodle');
            	  $name=uniqid().time().$file->getClientOriginalName();
            	  $filePath = 'profile_doodles/' . $name;
            	  $strg = Storage::disk('s3')->put($filePath, fopen($file, 'r+'),'public');
            	  $doodle_url = env('AWS_URL')."/".$filePath;

            	  $profile_doodle = new ProfileDoodle();
            	  $profile_doodle->user_id = Auth::user()->id;
            	  $profile_doodle->by_user_id = $id;
            	   $profile_doodle->media_url = $doodle_url;
            	  $profile_doodle->save();
            	 return response()->json(["status"=>true,"msg"=> "Doodle Request Sent.",],201);

            }
            else
            {
            	return response()->json(["status"=>false,"msg"=> "invalid request",],403);
            }






    }

    public function getDoodles($id = NULL){

    	if(!$id){$id = Auth::user()->id;}

    	$this->user = User::where('id',$id)
    	->orWhere('username',$id)
    	->first(['id','username']);

    	if($this->user)
    	{
    		
    		$doodle_data = ProfileDoodle::where('user_id',$id)->paginate(10);
    		return response()->json(["status"=>true,"data"=>$doodle_data],200);
    	}
    	else{
    		return response()->json(["status"=>false,"data"=>null],404);
    	}




    	

    }

}
