<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\Bookings;

class Trips extends Model
{
    protected $table = 'trips';
    use HasFactory;

    public function trips(){
        return $this->belongsTo(User::class, 'end_time', 'start_time');
    }

    public function bookings()
    {
        return $this->hasOne(Bookings::class);
    }
}
