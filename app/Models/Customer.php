<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Customer extends Model
{

    protected $table = 'customers';
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'home_phone',
        'work_phone',
        'customer_number',
        'driving_license',
        'date_of_birth',
        'anniversary',
        'age',
        'address',
        'address_2',
        'address_3',
        'city',
        'state',
        'zip',
        'country',
        'discovery_method',
        'avatar',
        'organization_id',
        'user_id',
    ];
    
    public function getAvatarUrl()
    {
        return Storage::url($this->avatar);
    }
}
