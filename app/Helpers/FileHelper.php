<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;

class FileHelper
{
    public static function storeFile($file, $folder = 'templates', $subfolder = 'default')
    {
        if ($file instanceof UploadedFile) {
            $year = now()->year;
            $date = now()->format('Y-m-d');
            $random = substr(uniqid(), -12);
            $extension = $file->getClientOriginalExtension();
            $filename = "{$date}-{$random}.{$extension}";

            $destination = public_path("storage/{$folder}/{$subfolder}/{$year}");
            $fullPath = "{$destination}/{$filename}";

            if (!file_exists($destination)) {
                mkdir($destination, 0775, true);
            }

            $file->move($destination, $filename);

            return "{$folder}/{$subfolder}/{$year}/{$filename}";
        }

        return is_string($file) ? $file : null;
    }
}
