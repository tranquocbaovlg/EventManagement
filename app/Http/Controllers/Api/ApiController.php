<?php

namespace App\Http\Controllers\Api;
use App\Event;
use App\Organizer;
use App\Attendee;
use App\Registration;
use App\Session_registration;
use App\Event_ticket;
use App\Http\Resources\EventRS;
use App\Http\Resources\ChannelRS;
use App\Http\Resources\TicketRS;
use App\Http\Resources\RegistrationRS;
use http\Env\Response;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    function events(){
        $events = Event::where("date", ">", "2021-09-00")->orderBy("date")->get();
        return response()->json([
            "events" => EventRS::collection($events)
        ]);
    }
    function organizers(Request $request){
        //Organizer $organizer, Event $event


        $organizer_slug = $request->route('organizer_slug');
        $event_slug = $request->route('event_slug');

        $organizer = Organizer::where('slug', $organizer_slug)->first();
        $event = Event::where('slug', $event_slug)->first();

        if ($organizer == null) return response()->json([
            'message' =>  "Organizer not found"
        ], 404);
        if ($event == null || $event->organizer->id != $organizer->id) return response()->json([
            'message' =>  "Event not found"
        ], 404);

//        dd($event);
        return response()->json([
            "id" => $event->id,
            "name" => $event->name,
            "slug" => $event->slug,
            "date" => $event->date,
            "channels" => ChannelRS::collection($event->channel),
            "tickets" => TicketRS::collection($event->ticket),
        ]);
    }

    function login(Request $request) {
        $attendee = Attendee::where('lastname', $request->lastname)
                            ->where('registration_code', $request->registration_code)
                            ->first();
        if ($attendee) {
            $attendee->login_token = md5($attendee->username);
            $attendee->save();
            return response()->json([
                "firstname"     => $attendee->firstname,
                "lastname"      => $attendee->lastname,
                "username"      => $attendee->username,
                "email"         => $attendee->email,
                "token"         => $attendee->login_token
            ]);
        } else {
            return response()->json([
                "message" => "Invalid login"
            ], 401);
        }
    }

    function logout(Request $request) {
        $attendee = Attendee::where('login_token', $request->token)->first();
        if($attendee != null){
            $attendee->login_token = '';
            $attendee->save();
            return response()->json([
                "message" => "Logout success"
            ]);
        }
        return response()->json([
            "message" => "Invalid token"
        ], 401);
    }

    function registration(Request $request) {
        $attendee = Attendee::where('login_token', $request->token)->first();
        if($attendee == null) return response()->json([
            "message" => "User not logged in"
        ], 401);

        $event_slug = $request->route('event_slug');
        //$organizer = Organizer::where('slug', $organizer_slug)->first();
        $event = Event::where('slug', $event_slug)->first();

        $tickets = $event->ticket->all();
        $registration_attendee = Registration::where('attendee_id', $attendee->id);
        foreach($tickets as $ticket){
            $registration = $registration_attendee->where('ticket_id', $ticket->id)->first();
            if($registration != null){
                return response()->json([
                    "message" => "User already registered"
                ], 401);
            }
        }

        $ticket = Event_ticket::where('id', $request->ticket_id)->first();
        $ticket->special_validity();
        if($ticket->available == false) return response()->json([
            "message" => "Ticket is no longer available"
        ], 401);

        $registration = Registration::first()->create([
            'attendee_id' => $attendee->id,
            'ticket_id' => $request->ticket_id,
            'registration_time' => date('Y-m-d H:i:s')
        ]);

        $session_ids = collect($request->session_ids);
        foreach($session_ids as $session_id){
            $registered = Session_registration::where([
                "registration_id" => $registration->id,
                "session_id" => $session_id
            ])->first();
            if($registered == null){
                Session_registration::first()->create([
                    "registration_id" => $registration->id,
                    "session_id" => $session_id
                ]);
            }
        }

        return response()->json([
            "message" => "Registration successful"
        ], 200);
    }

    function registrations(Request $request) {
        $attendee = Attendee::where('login_token', $request->token)->first();
        if($attendee == null) {
            return response()->json([
                "message" => "User not logged in"
            ], 401);
        }

        return response()->json([
            "registrations" => RegistrationRS::collection($attendee->registration)
        ]);
    }


}
