<?php

namespace App\Models;

<<<<<<< HEAD
use Illuminate\Database\Eloquent\Model;

class WhitelistTable extends Model
{
    protected $table = 'whitelist_tables'; 
=======
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static latest()
 */
class WhitelistTable extends Model
{
    use HasFactory;
>>>>>>> 03af03b40cddce6283cff9eee4cfe9d2c81dca2c
}
