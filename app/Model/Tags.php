<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Tags extends Model
{
    protected $table = 'tags';
    public $timestamps = false;

    public function materials() {
        return $this->belongsToMany(
                'App\Model\Material',
                'material_tag',
                'tag_id',
                'material_id'
            )
            ->withPivot('user_id');
    }
}
