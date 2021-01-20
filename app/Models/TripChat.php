<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;

class TripChat extends Model
{
    use HasFactory;

    protected $table = 'trip_chat';

    public function trip_chat(){
        return $this->belongsTo(User::class);
    }
}
