<?php

namespace App\Models;

use App\Http\Controllers\Traits\ImageTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Room extends Model
{
    use ImageTrait;

    public $timestamps = true;
    protected $guarded = [];

    public function images()
    {
        return $this->morphMany(Image::class, 'attachable');
    }

    public static function saveOne(Request $request)
    {
        $roomNewImages = array_values($request->allFiles());
        $roomExistingImages = $request->existingImages ?? [];
        $currentRoomImages = null;
        $deletedImagesIds = null;

        $room = self::updateOrCreate([
            'id' => $request->id
        ], $request->all());
        $currentRoomImages = $room->images()->get();

        if ($currentRoomImages->count() !== count($roomExistingImages)) {
            $deletedImagesIds = $currentRoomImages->filter(function ($roomImage) use ($roomExistingImages) {
                return !in_array($roomImage->url, $roomExistingImages);
            })->pluck('id');

            // self::deleteImages($deletedImagesIds);


            $room->images()->whereIn('id', $deletedImagesIds)->delete();
        }

        if (!empty($roomNewImages)) {
            //   $imagesUrls = self::attachImages($roomNewImages, 'rooms');
            //    $room->images()->createMany($imagesUrls);
        }
    }

    public function scopeGetList($query, Request $request)
    {
        return $query->selectRaw('
              rooms.id,
              rooms.floor,
              rooms.count_rooms,
              rooms.price,
              COUNT(guests.id) as popular,
              COUNT(users.id) as users
            ')
            ->join('guests', 'rooms.id', '=', 'guests.room_id')
            ->join('guest_user', 'guests.id', '=', 'guest_user.visit_id')
            ->join('users', 'guest_user.user_id', '=', 'users.id')
            ->groupBy('rooms.id')
            ->orderBy($request->order_type, $request->order);
    }

    public function currentUsers()
    {
        return $this->hasMany(Guest::class)
            ->with('users')
            ->whereRaw('UNIX_TIMESTAMP(guests.end) > ?', time())
            ->orderBy('end', 'desc');
    }

    public function guests()
    {
        return $this->hasMany(Guest::class);
    }

    public static function boot()
    {
        parent::boot();

        self::deleting(function ($room) {
            $imagesIds = $room->images()->get()->pluck('id');
            $room->images()->delete();
            self::deleteImages($imagesIds);
        });
    }
}
