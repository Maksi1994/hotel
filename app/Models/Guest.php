<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Guest extends Model
{

    public $timestamps = true;
    protected $guarded = [];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_gues', 'guest_id', 'user_id');
    }

    public static function saveOne(Request $request)
    {
        $guest = self::updateOrCreate(
            ['id' => $request->id],
            $request->only(['room_id'])
        );

        $guest->users()->sync($request->users);
    }

}
