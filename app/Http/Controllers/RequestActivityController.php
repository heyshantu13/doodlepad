<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\URL;


class RequestActivityController extends Controller
{
    public function index()
    {
        $user = Auth::user();

    $users = DB::table('request_activities')
        ->join('users', 'request_activities.follower_id', '=', 'users.id')
        ->join('user_profiles','request_activities.follower_id','=','user_profiles.user_id')
        ->select('users.id','users.fullname','users.username','request_activities.type','user_profiles.profile_picture_url','request_activities.created_at')
        ->paginate(config('constants.paginate_per_page'));
     return response()->json($users,200);


}


    public function acceptrequest(int $id){
      
    }


}
