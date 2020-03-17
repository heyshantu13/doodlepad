<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostActivity extends Model
{
    //
     public function getUserProfileIdAttribute($value)
    {
	return UserProfile::select(['id','gender', 'profile_picture_url','user_id'])->with('user:id,username')->find($value);
    }

    public function post()
    {
        return $this->belongsTo('App\Post');
    }

    // public function user(){
    // 	return $this->belongsTo(User::class, 'posts', 'user_id','id');
    // }

}
