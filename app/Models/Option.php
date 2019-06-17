<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Service;
use App\Models\Guest;

class Option extends Model
{
  public $guarded = [];
  public $timestamps = true;

  public function service() {
    return $this->belongsTo(Service::class);
  }

  public function guests() {
    return $this->belongsToMany(Guest::class, 'guest_service', 'options_id', 'guest_id');
  }
}
