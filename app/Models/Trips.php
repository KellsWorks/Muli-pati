<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;

class Trips extends Model
{
    protected $table = 'trips';
    use HasFactory;

    public function trips(){
        return $this->belongsTo(User::class, 'location', 'end_time', 'start_time');
    }
}
