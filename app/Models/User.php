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
    protected $guarded  =   [];

    protected $table = 'users';
    protected $fillable = [
        'organization_id', 'f_name', 'l_name', 'name', 'phone', 'home_phone', 'work_phone', 'email',
        'password', 'image', 'street_address', 'address_2', 'address_3', 'country', 'state', 'city',
        'zip', 'house_no', 'apartment_no', 'discovery_method', 'date_of_birth', 'anniversary', 'age',
        'probation', 'is_active', 'is_phone_verified', 'is_email_verified', 'payment_card_last_four',
        'payment_card_brand', 'payment_card_fawry_token', 'login_medium', 'social_id', 'facebook_id',
        'google_id', 'temporary_token', 'cm_firebase_token', 'wallet_balance', 'loyalty_point',
        'stripe_customer_id', 'liabilty_path', 'text_on_phone', 'ip_address', 'seasonal'
    ];
    

    


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'seasonal' => 'array'
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

    public function getFullNameAttribute(){
        if($this->name) {
            return $this->name;
        }
        return $this->f_name.' '.$this->l_name;
    }

    
    public function seasonalRenewal()
    {
        return $this->hasOne(SeasonalRenewal::class);
    }
}
