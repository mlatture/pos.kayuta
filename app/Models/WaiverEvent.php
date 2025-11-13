<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaiverEvent extends Model
{
    public $timestamps = false; // we use created_at only

    protected $fillable = [
        'waiver_id','actor_id','event','meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'created_at' => 'datetime',
    ];

    public function waiver()
    {
        return $this->belongsTo(Waiver::class);
    }
}
