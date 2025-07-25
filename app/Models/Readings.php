<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Readings extends Model
{
    use HasFactory;

    protected $table = 'readings';

    protected $fillable = ['kwhNo', 'image', 'date', 'siteno', 'status', 'bill', 'customer_id', 'meter_number'];

    public static function storeFile($file, $folder = 'meter_images')
    {
        if ($file instanceof \Illuminate\Http\UploadedFile) {
            $year = now()->year;
            $date = now()->format('Y-m-d');
            $random = substr(uniqid(), -12);
            $extension = $file->getClientOriginalExtension();
            $filename = "{$date}-{$random}.{$extension}";
    
            $destination = public_path("storage/{$folder}/{$year}");
            $fullPath = "{$destination}/{$filename}";
    
            if (!file_exists($destination)) {
                mkdir($destination, 0775, true);
            }
    

            $imageService = app('image');
            $resizedImage = $imageService->resize($file);
            $imageService->save($resizedImage, $fullPath);
            
            return "{$folder}/{$year}/{$filename}";
        }
    
        return is_string($file) ? $file : null;
    }
    
}