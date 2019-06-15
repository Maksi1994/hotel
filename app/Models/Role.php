<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{

    public $timestamps = true;
    public $guarded = [];

    public function users() {
        return $this->hasMany(User::class);
    }

    public static function getRegularUserId() {
        return self::where('name', 'regular')->get()->pluck('id')->first();
    }
}
