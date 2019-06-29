<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{

    public $timestamps = true;
    public $guarded = [];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function service()
    {
        return $this->belongsToMany(Service::class, 'role_service', 'role_id', 'service_id');
    }

    public static function getRegularUserId()
    {
        return self::where('name', 'regular')->get()->pluck('id')->first();
    }
}
