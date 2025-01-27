<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublishSurveyModel extends Model
{
    use HasFactory;

    protected $table = 'publish_surveys';

    protected $fillable = [
        'questions', 
        'answer_types',
        'title',
    ];

}

