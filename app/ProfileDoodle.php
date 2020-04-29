<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProfileDoodle extends Model
{
    //

    public function getMediaUrlAttribute($media_url){

    	return $media_url;

    }
}
