<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledPost extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'tenant_id', 'idea_id', 'scheduled_at',
        'platform_targets', 'post_payload', 'status', 'hq_job_id',
    ];

    protected $casts = [
        'scheduled_at'      => 'datetime',
        'platform_targets'  => 'array',
        'post_payload'      => 'array',
    ];

    public function idea()
    {
        return $this->belongsTo(ContentIdea::class, 'idea_id');
    }
}

