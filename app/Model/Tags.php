<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Tags extends Model
{
    public const PUBLIC_TRUE = true;
    public const PUBLIC_FALSE = false;
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
