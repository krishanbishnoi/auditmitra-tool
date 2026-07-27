<?php

namespace App\Helpers;

// use Illuminate\Support\Facades\Storage;
// use Intervention\Image\Facades\Image;

class ImageHelper
{
    public static function compressAndStore($imageUrl)
    {
        try {
            // Fetch image contents
            $imageContent = file_get_contents($imageUrl);
            if (!$imageContent) {
                return $imageUrl; // fallback if not found
            }

            // Create image instance
            $image = Image::make($imageContent);

            // Optional: resize (if too large)
            if ($image->width() > 1200) {
                $image->resize(1200, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
            }

            // Compress image (quality 70%)
            $filename = 'compressed/' . uniqid() . '.jpg';
            Storage::disk('public')->put($filename, (string) $image->encode('jpg', 70));

            // Return new public URL
            return asset('storage/' . $filename);
        } catch (\Exception $e) {
            return $imageUrl; // fallback if compression fails
        }
    }
}
