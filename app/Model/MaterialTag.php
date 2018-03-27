<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MaterialTag extends Model
{
    protected $table = 'material_tag';
    public $timestamps = false;
    public function user() {
        return $this->belongsTo('App\Model\Users', 'user_id', 'id');
    }
    public function material() {
        return $this->belongsTo('App\Model\Material', 'material_id', 'id');
    }
    public function tag() {
        return $this->belongsTo('App\Model\Tags', 'tag_id', 'id');
    }
}
