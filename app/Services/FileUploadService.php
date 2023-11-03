<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class FileUploadService
{
    public function uploadImage($image, $model = null)
    {
        if (!is_file($image)) {
            return $image;
        }
        return $this->uploadByType($image, $model);
    }

    private function uploadByType($image, $model)
    {
        $this->removeImageIfExists($model);

        $now = Carbon::now();
        $hash = Str::random(40);
        $image = Image::make($image);

        $image->encode('jpg', 20);
        $path = "uploads/user/" . $now->year . "/" . $now->month . "/" . $hash . ".webp";

        if (Storage::disk('public')->put($path, $image->__toString(), 'public')) {
            return $path;
        }
    }

    private function removeImageIfExists($model)
    {
        if ($model) {
            $file_path = $model->getAttributes()['profile_pic'];
            if ($model->profile_pic != null && Storage::disk('public')->exists($file_path)) {
                Storage::disk('public')->delete($file_path);
            }
        }
    }

    public function uploadImageUrl($url, $model = null)
    {
        if ($url) {
            $contents = file_get_contents($url);
            return $this->uploadByType($contents, $model);
        }
        return null;
    }
}
