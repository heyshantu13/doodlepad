<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
     public function getUserProfileIdAttribute($value)
    {
	return UserProfile::select(['id', 'user_id','profile_picture_url','is_private'])->find($value);
    }

 //     public function getUserIdAttribute($value)
 //    {
	// return User::select(['id', 'username','fullname'])->find($value);
 //    }

    public function comment_activities()
    {
        return $this->hasMany('App\CommentActivity');
    }
}
