<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{

    public $timestamps = true;
    public $guarded = [];


    public function attachable() {
        return $this->morphTo();
    }
}
