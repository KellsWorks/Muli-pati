<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Model\User;

class Profile extends Model
{
    use HasFactory;

    protected $table = 'profiles';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
