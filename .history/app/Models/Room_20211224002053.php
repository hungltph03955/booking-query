<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    public function type() {
        return $this->belongsTo('App\Models\RoomType', 'room_type_id', 'id');
    }

    public function reservations() {
        return $this->belongsToMany('App\Models\Reservation');
    }

    public function hotel() {
        return $this->belongsTo('App\Models\Reservation');
    }
}
