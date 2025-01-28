<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveysResponseModel extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'surveys_response';
    protected $fillable = [
        'email',
        'siteId',
        'survey_id',
        'questions',
        'answers',
        'token'
    ];
}
