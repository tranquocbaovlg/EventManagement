<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    public $timestamps = false;
    protected $guarded = [];

    function ticket() {
        return $this->belongsTo(Event_ticket::class, 'ticket_id');
    }
    function session_registration() {
        return $this->hasMany(Session_registration::class, 'registration_id');
    }
}
