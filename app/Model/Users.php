<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    protected $table = 'users';
    public $timestamps = false;

    public function materials()
    {
        return $this->hasMany('App\Model\Material', 'user_id', 'id');
    }
}
