<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Option;
use App\Models\Guest;
use Illuminate\Http\Request;

class Service extends Model
{
    public $guarded = [];
    public $timestamps = true;

    public function options() {
      return $this->hasMany(Option::class);
    }

    public function guests() {
      return $this->belongsToMany(Guest::class,  'guest_service', 'service_id', 'guest_id');
    }

    public static function saveOne(Request $request) {
      $serviceModel = self::updateOrCreate([
        'id' => $request->id
      ], [
        'name' => $request->name
      ]);

      $serviceModel->options()->delete();
      $serviceModel->options()->createMany($request->options);
    }

}
