<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\UserProfile;
use App\User;

class DoodleController extends Controller
{

    // public function __construct(){

    // }
    
    public function index(){
        $uid = Auth::user()->id;
    }

}
