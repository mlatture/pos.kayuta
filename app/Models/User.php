<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $table = 'users';
    protected $fillable = [
        'f_name',
        'l_name',
        // 'name', // removed: column dropped from users table
        'email',
        'stripe_customer_id',
        'password',
        'phone',
        'image',
        'login_medium',
        'is_active',
        'social_id',
        'is_phone_verified',
        'temporary_token',
        'home_phone',
        'customer_number',
        'work_phone',
        'driving_license',
        'street_address',
        'address_2',
        // 'address_3', // removed: column dropped from users table
        'city',
        'state',
        'zip',
        'country',
        'discovery_method',
        'probation',
        'date_of_birth',
        'anniversary',
        'age',
        'two_factor_enabled',
        'two_factor_secret',
        'two_factor_backup_codes',
        'phone_verified_at',

        // Second guest fields
        'guest2_f_name',
        'guest2_l_name',
        'guest2_email',
        'guest2_phone',
        'guest2_can_text',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'seasonal' => 'array',
    ];

    public function getAvatarUrl()
    {
        return Storage::url($this->image);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'customernumber');
    }

    public function latestReservation()
    {
        return $this->hasOne(Reservation::class, 'customernumber', 'id')->latestOfMany();
    }

    public function cart_reservations()
    {
        return $this->hasMany(CartReservation::class, 'customernumber');
    }

    public function cardsOnFile()
    {
        return $this->hasMany(CardsOnFile::class, 'customernumber');
    }

    public function cartReservations()
    {
        return $this->hasMany(CartReservation::class, 'customernumber');
    }

    public function receipts()
    {
        return $this->hasManyThrough(Receipt::class, Reservation::class, 'customernumber', 'cartid', 'id', 'cartid');
    }

    public function findUserById($id)
    {
        return self::find($id);
    }

    public function getFullNameAttribute()
    {
        if ($this->name) {
            return $this->name;
        }
        return $this->f_name . ' ' . $this->l_name;
    }

    public function seasonalRenewal()
    {
        return $this->hasOne(SeasonalRenewal::class, 'email');
    }

    public function getSeasonalRatesAttribute()
    {
        $ids = is_array($this->seasonal) ? $this->seasonal : json_decode($this->seasonal, true);
        return SeasonalRate::whereIn('id', $ids ?: [])->get();
    }
}
