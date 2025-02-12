<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdminRole extends Model
{
    protected $table = 'admin_roles';
    protected $guarded = [];

    protected $casts = [
        'module_access' => 'array',
    ];

    public function getModuleAccessAttribute($value)
    {
        return is_string($value) ? json_decode($value, true) : ($value ?? []);
    }

    private function getDashboardModule()
    {
        return config('constants.role_modules.dashboard.value', 'dashboard');
    }

    public function admins(): HasMany
    {
        return $this->hasMany(Admin::class, 'admin_role_id', 'id');
    }
}
