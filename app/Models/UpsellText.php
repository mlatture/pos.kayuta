<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UpsellText extends Model
{
    use HasFactory;

    protected $table = 'upsell_text';

    protected $fillable = ['message_text', 'active_message', 'item_numbers'];
}
