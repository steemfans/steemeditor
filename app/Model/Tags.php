<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Tags extends Model
{
    public static PUBLIC_TRUE = true;
    public static PUBLIC_FALSE = false;
    protected $table = 'tags';
    public $timestamps = false;

    protected $attributes = [
        'public' => self::PUBLIC_TRUE,
    ];

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
