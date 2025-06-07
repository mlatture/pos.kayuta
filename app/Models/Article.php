<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory;

    protected $table = 'articles';

    protected $fillable = ['title', 'slug', 'description', 'thumbnail', 'status', 'metatitle', 'metadescription', 'canonicalurl', 'opengraphtitle', 'opengraphdescription', 'opengraphimage'];

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;

        do {
            $baseSlug = Str::slug(Str::words($value, 4, ''));
            $randomSuffix = Str::lower(Str::random(4));
            $slug = "{$baseSlug}-{$randomSuffix}";
        } while (static::where('slug', $slug)->exists());

        $this->attributes['slug'] = $slug;
    }

    public function setThumbnailAttribute($file)
    {
        if (is_file($file)) {
            $date = now()->format('Y-m-d');
            $random = substr(uniqid(), -12);
            $extension = $file->getClientOriginalExtension();
            $filename = "{$date}-{$random}.{$extension}";
            $file->move(public_path('storage/articles'), $filename);
            $this->attributes['thumbnail'] = $filename;
        } else {
            $this->attributes['thumbnail'] = $file;
        }
    }

    public function setOpengraphimageAttribute($file)
    {
        if (is_file($file)) {
            $date = now()->format('Y-m-d');
            $random = substr(uniqid(), -12);
            $extension = $file->getClientOriginalExtension();
            $filename = "{$date}-og-{$random}.{$extension}";
            $file->move(public_path('storage/articles'), $filename);
            $this->attributes['opengraphimage'] = $filename;
        } elseif (is_string($file)) {
            $this->attributes['opengraphimage'] = $file;
        }
    }

    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = $value ?? 0;
    }
}
