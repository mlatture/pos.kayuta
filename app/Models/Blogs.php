<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Blogs extends Model
{
    use HasFactory;

    protected $table = 'blogs';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'image',
        'status',
        'metatitle',
        'metadescription',
        'canonicalurl',
        'opengraphtitle',
        'opengraphdescription',
        'opengraphimage',
    ];

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        if (!isset($this->attributes['slug']) || empty($this->attributes['slug'])) {
            $this->attributes['slug'] = Str::slug($value);
        }
    }

    public function setImageAttribute($file)
    {
        if (is_file($file)) {
            $date = now()->format('Y-m-d');
            $random = substr(uniqid(), -12);
            $extension = $file->getClientOriginalExtension();
            $filename = "{$date}-{$random}.{$extension}";
            $file->move(public_path('storage/blogs'), $filename);
            $this->attributes['image'] = $filename;
        } elseif (is_string($file)) {
            $this->attributes['image'] = $file;
        }
    }

    public function setOpengraphimageAttribute($file)
    {
        if (is_file($file)) {
            $date = now()->format('Y-m-d');
            $random = substr(uniqid(), -12);
            $extension = $file->getClientOriginalExtension();
            $filename = "{$date}-og-{$random}.{$extension}";
            $file->move(public_path('storage/blogs'), $filename);
            $this->attributes['opengraphimage'] = $filename;
        } elseif (is_string($file)) {
            $this->attributes['opengraphimage'] = $file;
        }
    }

    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = $value ? 1 : 0;
    }
}
