<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserContactList extends Model
{
    //

    protected $table = 'user_contact_sync_lists';

    protected $fillable = ['data','user_id'];

}
