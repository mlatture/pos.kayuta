<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;
    protected $guarded = [];

    public function getAvatar()
    {
        return 'https://www.gravatar.com/avatar/' . md5($this->email);
    }


    public function getFullname(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }


    public function cart()
    {
        return $this->belongsToMany(Product::class, 'admin_cart')->withPivot('quantity', 'discount', 'tax')->with(['taxType']);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(AdminRole::class,'admin_role_id','id');
    }

    public function hasPermission(string $permission){
        if($this->role->id == 1) {
            return true;
        }
        return in_array($permission,$this->role->module_access ?? []);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class,'organization_id','id');
    }
}
