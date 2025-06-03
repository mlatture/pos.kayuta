<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromptTemplate extends Model
{
    use HasFactory;
    protected $table = 'prompt_templates';
    protected $fillable = ['type', 'system_prompt', 'user_prompt'];
}
