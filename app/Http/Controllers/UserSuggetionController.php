<?php


/*

		@author: Shantanu Kulkarni
		@date: 15/08/2020
		@git: heyshantu13

*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\UserProfile;
use App\Follower;
use App\UserContactList;


class UserSuggetionController extends Controller
{
   

    private $syncdata;
    private $userId;


    // Sync User Contact and Store In To Database


    /**
     * Login user and create token
     * @param  [json_array] syncdata = [{},{},{}.....{}]
     * @return [boolean] status
     * @return [string] action
     */



    public function store(Request $request){


    	$request->validate([
    		'syncdata' => 'required|array'
    	]);

    	$this->userId = Auth::user()->id;

    	$this->syncdata = json_encode($request->syncdata);

    	$insertData = array(
    		'data' => $this->syncdata,
    		'user_id' => Auth::user()->id,
    	);

    	$stored = UserContactList::create($insertData);

    	try {


    	if($stored)
    	{
    		return response()->json(['status'=>true,'action'=>'none'],200);
    	}
    	else{
    		return response()->json(['status'=>false,'action'=>'retry'],403);
    	}
       

    	} catch (Throwable $e) {

    		return response()->json(['status'=>false,'action'=>'retry'],400);
        

        }

    }



    /* Check If Contact List Available Or Not*/


    private function checkContactSynced()
    {
    	$synced = UserContactList::where('user_id',Auth::user()->id);
    	
    	if($synced)
    	{
    		return true;
    	}
    	else{
    		return false;
    	}
    }




    /* Get */


}
