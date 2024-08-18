<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdminRole extends Model
{

    protected $table = 'admin_roles';
    protected $guarded = [];

    protected $casts = ['module_access' => 'array'];

    public function admins(): HasMany
    {
        return $this->hasMany(Admin::class,'admin_role_id','id');
    }

}
