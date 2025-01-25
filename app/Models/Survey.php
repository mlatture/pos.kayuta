<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\ProcessSurveyEmail;
use Illuminate\Support\Facades\Log;

class Survey extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'surveys';
    protected $fillable = [
        'guest_email', 'subject', 'message', 'sent', 'created_at',
    ];

    protected static function booted()
    {
        static::created(function ($survey) {
            ProcessSurveyEmail::dispatch($survey);
        });
    }
    
}
