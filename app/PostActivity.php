<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostActivity extends Model
{
    //
     public function getUserProfileIdAttribute($value)
    {
	return UserProfile::select(['id', 'bio','gender', 'profile_picture_url'])->find($value);
    }

    public function post()
    {
        return $this->belongsTo('App\Post');
    }

}
