<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\URL;
use App\RequestActivity;
use App\Follower;
use App\Helpers\FollowerHelper;


class RequestActivityController extends Controller
{
    public function index()
    {
        $user = Auth::user();

    $users = DB::table('request_activities')
        ->join('users', 'request_activities.follower_id', '=', 'users.id')
        ->join('user_profiles','request_activities.follower_id','=','user_profiles.user_id')
        ->select('users.fullname','users.username','request_activities.type','user_profiles.profile_picture_url','request_activities.created_at','request_activities.id as RequestID')
        ->where('request_activities.user_id','=',auth()->user()->id)
        ->paginate(config('constants.paginate_per_page'));
     return response()->json($users,200);


}


    public function acceptRequest(Request $request){
       $requestData =  RequestActivity::where('id',$request->id)->first();
    if($requestData){
        $follower = new Follower();
        $follower->follower_id =$requestData->follower_id;
        $follower->user_id = $requestData->user_id;
        $follower->save();
        $requestData->delete();
        FollowerHelper::FollowerActivity($follower->follower_id,$follower->user_id,"APPROVED");
        return response()->json(["status"=>true],200);

    }
    else{
        return response()->json(["status"=>false],400);
    }

    }

    public function rejectRequest(Request $request){
        $requestData =  RequestActivity::where('id',$request->id)->first();
        if($requestData){
             $requestData->delete();
              return response()->json(["status"=>true],200);
        }
        else{
             return response()->json(["status"=>false],400);
        }
    }


}
