<?php


namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageService
{
    /**
     * Save the image and return its path.
     *
     * @param \Illuminate\Http\UploadedFile $image
     * @param string $folder
     * @return string
     */
    public function saveImage($image, $folder)
    {
        $filename = uniqid('', true) . '.' . $image->getClientOriginalExtension();
        $filePath = $folder . '/' . $filename;

        $image->storeAs($folder, $filename, 'public');
       
        return $filePath;
    }

    public function deleteImage(?string $imagePath): bool
    {
        if (empty($imagePath)) {
            return false;
        }

        $storagePath = str_replace('storage/', '', $imagePath);
        if (Storage::disk('public')->exists($storagePath)) {
            return Storage::disk('public')->delete($storagePath);
        }
        return false;
    }

}