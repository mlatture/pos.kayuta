<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    protected $table = 'organizations';
    protected $guarded = [];
    protected $appends = ['full_address'];

    public function admins(): HasMany
    {
        return $this->hasMany(Admin::class,'organization_id','id');
    }

    public function getFullAddressAttribute(): string
    {
        return $this->address_1.', '.$this->address_2.', '.$this->city.', '.$this->state.' '.$this->zip.', '.$this->country;
    }
}
