<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentTemplate extends Model
{
    use HasFactory;

    protected $table = 'document_templates';

    protected $fillable = ['name', 'description', 'file', 'is_active'];

    protected $casts = ['created_at' => 'date'];

    public function seasonalRates()
    {
        return $this->hasMany(SeasonalRate::class, 'template_id');
    }
}
