<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
	protected $hidden = ['deleted_at','created_at'];

     public function getUserProfileIdAttribute($value)
    {
	return UserProfile::select(['id', 'user_id','profile_picture_url','is_private'])->with('user:id,username')->find($value);
    }

 
    public function comment_activities()
    {
        return $this->hasMany('App\CommentActivity');
    }
}
