<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Overtrue\LaravelFollow\Traits\CanFollow;
use Overtrue\LaravelFollow\Traits\CanBeFollowed;


class UserProfile extends Model
{
    use  CanFollow, CanBeFollowed;

     protected $fillable = [
        'bio', 'date_of_birth', 'gender','profile_picture_url'
    ];

    public function followers()
    {
        return $this->hasMany('Overtrue\LaravelFollow\FollowRelation', 'followable_id');
    }

    public function following()
    {
        return $this->hasMany('Overtrue\LaravelFollow\FollowRelation');
    }

}
