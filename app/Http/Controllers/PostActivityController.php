<?php

/*  Author:  Shantanu K
    Git: heyshantu13
    Description:  Authentication Management Controller
*/



namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PostActivity;
use App\UserProfile;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\FollowActivity;
use App\CommentActivity;

class PostActivityController extends Controller
{
    public function index()
    {
       $user = Auth::user();
       $profile_id = UserProfile::where('user_id',$user->id)->first(['id','user_id']);

    // $posts =  Post::where('user_profile_id',$profile_id->id)->pluck('id');


       /*
                use doodlepad;

Select users.id as uid,users.username as username, u1.profile_picture_url,p1.*
From `post_activities` as p1
join `user_profiles` as u1 on u1.id = p1.user_profile_id 
join `users` on users.id = u1.user_id
WHEREIN p1.user_profile_id = 11,12


       */

    $currentPage = LengthAwarePaginator::resolveCurrentPage();

    $notifications = DB::table('post_activities as pa')
        ->join('user_profiles as up','up.id','=','pa.user_profile_id')
        ->join('users as u','u.id','=','up.user_id')
        ->select('u.id as user_id','u.username as username','up.profile_picture_url as profile_picture_url','pa.id','pa.user_profile_id as user_profile_id','pa.type as type','pa.post_id','pa.created_at')
        ->where('u.id',$user->id)
        ->orderBy('pa.created_at','DESC')
        ->get();
        // ->paginate(config('constants.paginate_per_page'));

    $follower_activity = DB::table('follower_activities')
    ->join('users as u','u.id','=','follower_activities.follower_id')
    ->join('user_profiles as up','up.user_id','=','follower_activities.follower_id')
    ->select('follower_activities.id','follower_activities.follower_id as user_id','follower_activities.type','u.username','up.profile_picture_url')
    ->where('follower_activities.user_id',$user->id)
    ->orderBy('follower_activities.created_at','DESC')
    ->get();

    // $comments_activity = CommentActivity::select()
    // ->join()

    $activities = array_merge($notifications->toArray(), $follower_activity->toArray());
    
    $currentPage = LengthAwarePaginator::resolveCurrentPage();
    $perPage = 8;

    $currentItems = array_slice($activities, $perPage * ($currentPage - 1), $perPage);

    $paginator = new LengthAwarePaginator($currentItems, count($activities), $perPage, $currentPage);


    return response()->json($paginator,200);

 

 }


}
