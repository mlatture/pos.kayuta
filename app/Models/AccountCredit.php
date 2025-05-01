<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountCredit extends Model
{
    use HasFactory;

    protected $table = 'account_credits';
    protected $fillable = [
        'email',
        'credit',
        'used_credit'
    ];
}
