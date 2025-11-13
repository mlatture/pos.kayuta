<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class Waiver extends Model
{


    protected $fillable = [
        'customer_id','booking_id','name','email','site_text',
        'pdf_path','doc_hash','ip','ua','status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships (adjust model names/namespaces as per your app)
    public function customer()
    {
        return $this->belongsTo(User::class);
    }

    public function events()
    {
        return $this->hasMany(WaiverEvent::class);
    }
}
