<?php

namespace App\Models;

use App\Http\Controllers\Traits\ImageTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
        $query->when($request->orderType === 'new' , function ($q) use ($request) {
            $q->created('created_at', $request->order ?? 'desc');
        });

        $query->when($request->orderType === 'price' , function ($q) use ($request) {
            $q->created('created_at', $request->order ?? 'desc');
        });

        return $query;
    }

    public function currentGuest()
    {
      return $this->hasMany(Guest::class)
      ->with('users')
      ->orderBy('created_at', 'desc')
      ->where('end', Carbon)
      ->first();
    }

    public function guests() {
        return $this->hasMany(Guest::class)->with('users');
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
