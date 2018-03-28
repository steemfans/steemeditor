<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $table = 'material';
    public $timestamps = false;

    public function tags() {
        return $this->belongsToMany(
                'App\Model\Tags',
                'material_tag',
                'material_id',
                'tag_id'
            )
            ->withPivot('user_id');
    }

    public function user() {
        return $this->belongsTo('App\Model\Users', 'user_id', 'id');
    }
}
