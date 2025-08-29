<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Readings extends Model
{
    use HasFactory;

    protected $table = 'electric_readings';

    protected $fillable = [
        'kwhNo',
        'meter_number',
        'image',
        'date',
        'meter_style',
        'manufacturer',
        'ai_meter_number',
        'ai_meter_reading',
        'ai_success',
        'ai_fixed',
        'prompt_version',
        'model_version',
        'ai_latency_ms',
    ];

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