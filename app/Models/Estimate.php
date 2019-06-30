<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estimate extends Model
{
    public $guarded = [];
    public $timestamps = true;

    public function estimable() {
      return $this->morpthTo();
    }

    public function scopeGetList($query, Request $request) {

    }
}
