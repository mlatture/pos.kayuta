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
        'status'
    ];


    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        $this->attributes['slug'] = Str::slug($value);


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
        } else {
            $this->attributes['image'] = $file;
        }
    }

    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = $value ?? 0;
    }

}
