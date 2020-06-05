<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AppController extends Controller
{
    //


    public function privacyAndPolicy(){
    	return view('app.privacynpolicy');
    }
}
