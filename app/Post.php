<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Carbon\Carbon;

class Post extends Model
{
    protected $fillable = [
        'bio', 'date_of_birth', 'gender','profile_picture_url'
    ];

    protected $hidden = [
    	'deleted_at','filename'
    ];

    public function post_activities()
    {
        return $this->hasMany('App\PostActivity');
    }

     public function user()
    {
        return $this->belongsTo('App\User')->select(['id','username']);;
    }

    public function userprofile()
    {
        return $this->belongsTo('App\UserProfile','user_profile_id')->select(['id','profile_picture_url']);
    }

    public function getCreatedAtAttribute($date)
{
    return Carbon::create($date)->diffForHumans();
}
    
     public function getUpdatedAtAttribute($date)
{
    return Carbon::create($date)->diffForHumans();
}
    

}
