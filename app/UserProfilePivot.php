<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;


class UserProfilePivot extends Pivot
{
    //
    protected $table = 'user_details';
}
