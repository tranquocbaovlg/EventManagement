<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attendee extends Model
{
    public $timestamps = false;
    protected $guarded = [];

    function registration() {
        return $this->hasMany(Registration::class, "attendee_id");
    }
}
