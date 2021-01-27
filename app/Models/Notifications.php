<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Notifications;

class NotificationsModel extends Model
{
    use HasFactory;

    protected $table = 'user_notifications';

    public function notifications(){
        return $this->belongsTo(User::class);
    }
}
