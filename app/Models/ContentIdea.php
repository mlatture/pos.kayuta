<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



// app/Models/ContentIdea.php
class ContentIdea extends Model
{
        use HasFactory;

    protected $fillable = [
        'tenant_id', 'category_id', 'title', 'summary', 'rank',
        'status', 'ai_inputs',
    ];

    protected $casts = [
        'ai_inputs' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
      public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }


    public function scheduledPosts()
    {
        return $this->hasMany(ScheduledPost::class, 'idea_id');
    }
}
