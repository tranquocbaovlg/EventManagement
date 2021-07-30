<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RegistrationRS extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
//        dd($this->session_registration);
        return [
            "event" => new EventRS($this->ticket->event),
            "session_ids" => Session_registrationRS::collection($this->session_registration)
        ];
    }
}
