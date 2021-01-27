<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Models\Profile;
use App\Models\Trips;
use App\Models\TripChat;
use App\Models\Notifications;
use App\Models\Bookings;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'phone',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function trip()
    {
        return $this->hasOne(Trips::class);
    }

    public function trip_chat()
    {
        return $this->hasOne(TripChat::class);
    }

    public function notifications()
    {
        return $this->hasOne(Notifications::class);
    }
    public function bookings()
    {
        return $this->hasOne(Bookings::class);
    }
}
