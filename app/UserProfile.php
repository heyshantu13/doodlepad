<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;



class UserProfile extends Model
{


     protected $fillable = [
        'bio', 'date_of_birth', 'gender','profile_picture_url','fcm_registration_id'
    ];

    public function user(){
        return $this->hasOne('App\User');
    }

     public function activities()
    {
        return $this->hasManyThrough('App\PostActivity', 'App\Post', 'user_profile_id', 'post_id');
    }

    public function posts()
    {
        return $this->hasMany('App\Post');
    }



  

}
