<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PostActivity extends Model
{
    //
     public function getUserProfileIdAttribute($value)
    {
	return UserProfile::select(['id','is_private', 'profile_picture_url','user_id'])->with('user:id,username')->find($value);
    }

    public function post()
    {
        return $this->belongsTo('App\Post');
    }

    // public function user(){
    // 	return $this->belongsTo(User::class, 'posts', 'user_id','id');
    // }

       public function getCreatedAtAttribute($date)
{
    return Carbon::create($date)->diffForHumans();
}
    

}
