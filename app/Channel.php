<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
//use MongoDB\Driver\Session;
use App\Session;

class Channel extends Model
{
    public $timestamps = false;
    protected $guarded = [];

    function room() {
        return $this->hasMany(Room::class, "channel_id");
    }

    function session() {
        return $this->hasManyThrough(Session::class, Room::class, "channel_id", "room_id");
    }

    function event() {
        return $this->belongsTo(Event::class, "event_id");
    }

}
