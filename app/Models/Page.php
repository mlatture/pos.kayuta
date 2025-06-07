<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class Page extends Model
{
    use HasFactory;

    protected $table = 'pages';

    protected $fillable = ['title', 'slug', 'description', 'type', 'status', 'image', 'attachment', 'metatitle', 'metadescription', 'canonicalurl', 'opengraphimage', 'opengraphtitle', 'opengraphdescription', 'schema_code_pasting'];

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;

        if (empty($this->attributes['slug'])) {
            $this->attributes['slug'] = Str::slug($value);
        }
    }

    public function setImageAttribute($file)
    {
        $this->attributes['image'] = $this->storeFile($file, 'pages');
    }

    public function setAttachmentAttribute($file)
    {
        $this->attributes['attachment'] = $this->storeFile($file, 'attachments');
    }

    public function setOpengraphimageAttribute($file)
    {
        $this->attributes['opengraphimage'] = $this->storeFile($file, 'opengraph');
    }

    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = $value ?? 0;
    }

    protected function storeFile($file, $folder)
    {
        if (is_a($file, \Illuminate\Http\UploadedFile::class)) {
            $date = now()->format('Y-m-d');
            $random = substr(uniqid(), -12);
            $extension = $file->getClientOriginalExtension();
            $filename = "{$date}-{$random}.{$extension}";

            $file->move(public_path("storage/{$folder}"), $filename);

            return $filename;
        }

        return is_string($file) ? $file : null;
    }
}
