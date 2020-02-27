<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable,HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fullname', 'email', 'password','mobile','username'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function userprofiles(){
        return $this->hasMany('App\UserProfile','user_id','id')->select(['user_id','bio','profile_picture_url','is_private','gender']);
    }

	    /**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function followers()
	{
	    return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id')->using('App\UserProfilePivot')->withTimestamps();
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function followings()
	{
	    return $this->belongsToMany(User::class, 'followers', 'follower_id', 'user_id')->as('details')->using('App\UserProfilePivot');

	    
	}

	public function posts()
    {
        return $this->hasMany('App\Post');
    }





   
}
