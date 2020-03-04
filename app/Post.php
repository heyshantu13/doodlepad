<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;


class Post extends Model
{
    protected $fillable = [
        'bio', 'date_of_birth', 'gender','profile_picture_url'
    ];

    public function post_activities()
    {
        return $this->hasMany('App\PostActivity');
    }

}
