<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'categories';
    protected $fillable = [
        'name',
        'quick_books_account_name',
        'account_type',
        'notes',
        'status',
        'show_in_pos'
    ];
}
