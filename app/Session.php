<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    public $timestamps = false;
    protected $guarded = [];

    function room() {
        return $this->belongsTo(Room::class, "room_id");
    }
    function session_registration() {
        return $this->hasMany(Session_registration::class, 'session_id');
    }
}
