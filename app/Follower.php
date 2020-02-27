<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Follower extends Model
{
    public function details(){
    	return $this->hasOne('App\UserProfile');
    }
}
