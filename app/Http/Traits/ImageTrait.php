<?php

namespace App\Http\Controllers\Traits;

use App\Models\Image;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

trait ImageTrait {

    public static function deleteImages($ids) {
        $images = Image::find($ids);

        foreach ($images as $image) {
            Storage::dist('store')->delete($image->name);
        }
    }

    public static function attachImages($images, $folder) {
        $newImagesUrls = [];

        foreach ($images as $image) {
            $newImagesUrls[] = Storage::dist('store')->putFile($folder, $image['name'], 'public');
        }

        return $newImagesUrls;
    }


}
