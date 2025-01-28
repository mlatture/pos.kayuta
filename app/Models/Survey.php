<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\ProcessSurveyEmail;
use Illuminate\Support\Facades\Log;
use SurveysResponseModel;

class Survey extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'surveys';
    protected $fillable = [
        'name','survey_id','guest_email','siteId', 'token', 'subject', 'message', 'sent', 'created_at',
    ];


    protected static function booted()
    {
        static::created(function ($survey) {
            Log::info("Survey has been created.", ['survey' => $survey->toArray()]);
            ProcessSurveyEmail::dispatch($survey);
        });
    }
    
}
