<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Trips;
use App\Models\User;

class Bookings extends Model
{
    use HasFactory;

    protected $table = 'bookings';

    public function bookings(){
        return $this->belongsTo(User::class);
    }
}
