<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RequestActivity;
use App\UserProfile;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RequestActivityController extends Controller
{
     public function index()
    {
        $user = Auth::user();
        $profile = UserProfile::where('user_id', $user->id)
        ->firstOrFail();

    $activities = RequestActivity::whereHas('post',function($query) use($profile) 
    {
        $query->where('user_profile_id', $profile->id);})
        ->whereNotIn('user_profile_id', [$profile->id])
        ->orderBy('created_at', 'desc')
        ->paginate(config('constants.paginate_per_page'));
        return response()->json($activities,200);
    }
}
