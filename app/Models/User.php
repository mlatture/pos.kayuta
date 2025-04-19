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
        'id',
        'f_name',
        'l_name'
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
    ];

    public function getAvatarUrl()
    {
        return Storage::url($this->image);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'customernumber');
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

    
}
