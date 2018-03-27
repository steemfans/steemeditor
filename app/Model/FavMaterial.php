<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class FavMaterial extends Model
{
    protected $table = 'fav_material';
    public $timestamps = false;

    public function user() {
        return $this->belongsTo('App\Model\Users', 'user_id', 'id');
    }

    public function material() {
        return $this->belongsTo('App\Model\Material', 'material_id', 'id');
    }
}
