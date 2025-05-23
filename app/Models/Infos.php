<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Infos extends Model
{
    use HasFactory;


    protected $table = 'infos';
    protected $fillable = [
        'title',
        'description',
        'status',
        'show_in_details',
        'order_by',
        'auto_correct',
        'ai_rewrite'
    ];



    
}
