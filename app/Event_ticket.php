<?php

namespace App;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Database\Eloquent\Model;
use App\Event;
//use Nexmo\Call\Event;

class Event_ticket extends Model
{
    public $timestamps = false;
    protected $guarded = [];

    function registration(){
        return $this->hasMany(Registration::class, "ticket_id");
    }

    function event() {
        return $this->belongsTo(Event::class, "event_id");
    }

    function special_validity(){
        $temp = json_decode($this->special_validity, true);
        $this->available = true;
        $this->description = "";
        if ($temp != null){
            if ($temp['type'] == 'date'){
                $this->description = 'Available until ' . date('F j, Y', strtotime($temp['date']));
                if ($temp['date'] < '2021-09-00') {
                    $this->available = false;
                }
            }
            else {
                $this->description = $temp['amount'] . ' ticket' . ($temp['amount']>1?'s':'') . ' available';
                if ((int)$temp['amount'] <= $this->registration->count()){
                    $this->available = false;
                }
            }
        }
    }
}
