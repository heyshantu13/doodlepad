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
        $user = User:where('id',$user->id)->firstOrFail();

}
