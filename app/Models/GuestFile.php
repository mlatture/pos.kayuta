<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuestFile extends Model
{
    use HasFactory;

    public const PROTECTED_CATEGORIES = ['contracts', 'renewals', 'non_renewals'];


    protected $table = 'guest_files';
    protected $fillable = ['customer_id', 'reservation_id', 'name', 'file_category', 'file_path', 'expiration_date'];

    protected $casts = [
        'expiration_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    public function isProtected(): bool
    {
        return in_array($this->file_category, self::PROTECTED_CATEGORIES, true);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id', 'id');
    }
}
