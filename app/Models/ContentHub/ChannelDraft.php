<?php

namespace App\Models\ContentHub;

use Illuminate\Database\Eloquent\Model;

class ChannelDraft extends Model
{
    protected $fillable = [
      'batch_id','channel','draft_content','system_notes','version',
      'status','scheduled_at','published_at','publish_result'
    ];

    protected $casts = [
      'publish_result' => 'array',
      'scheduled_at' => 'datetime',
      'published_at' => 'datetime',
    ];

    public function batch(){ return $this->belongsTo(ContentBatch::class,'batch_id'); }
    public function versions(){ return $this->hasMany(DraftVersion::class,'draft_id'); }
}
