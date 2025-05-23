<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $table = 'pages';
    protected $fillable = [
        'title', 'slug', 'description', 'type', 'status',
        'image', 'attachment', 'metatitle', 'metadescription', 'canonicalurl',
        'opengraphimage', 'opengraphtitle', 'opengraphdescription',
        'schema_code_pasting'
    ];
    
}
