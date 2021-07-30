<?php

namespace App\Http\Controllers;

use App\Event_ticket;
use http\Client\Curl\User;
use Illuminate\Http\Request;
use App\Event;
use Illuminate\Support\Facades\Auth;
use function GuzzleHttp\Psr7\str;

//use Illuminate\Support\Facades\Event;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public $ticket;

    public function __construct()
    {
//        $this->ticket = Event_ticket::first();
    }

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Event $event)
    {
        //$event = Event::where("id", $request->event_id)->get()->first();  //lara tự hỉu
        if(Auth::user()->id != $event->organizer->id) {
            abort(404);
        }
        return view('ticket.create', compact("event"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Event $event, Request $request)
    {
        $special_validity = $request->special_validity;
        $validate_amount = '';
        $validate_valid_until = '';
        if($special_validity == 'amount') {
            $validate_amount = 'required|numeric|min:0';
        } elseif($special_validity == 'date') {
            $validate_valid_until = 'required|date_format:Y-m-d H:i|after:tomorrow';
        }

        $data = $request->validate([
            "name"              => "required",
            "cost"              => "required",
            "amount"            => $validate_amount,
            "valid_until"       => $validate_valid_until,
        ], [
            "name.required"             => "Name is required.",
            "cost.required"             => "Cost is required.",
            "amount.required"           => "The amount field is required when special validity is amount.",
            "valid_until.required"      => "The valid until field is required when special validity is date.",
            "valid_until.date_format"   => "The valid until format must be yyyy-mm-dd HH:MM",
            "valid_until.after"         => "The valid until must be in the future",
        ]);

        $newTicket = [
            "name" => $request->name,
            "cost" => $request->cost
        ];

        $newTicket["special_validity"] = json_encode([
            "type" => $special_validity,
            $special_validity => $special_validity=='date'?date('Y-m-d', strtotime($request->valid_until)):$request->$special_validity
        ]);
        if($special_validity=='') {
            $newTicket["special_validity"] = null;
        }

        if(Auth::user()->id != $event->organizer->id) {
            abort(404);
        }

        $event->ticket()->create($newTicket);

        return redirect()->route("event.show", $event->id)->with([
            "message" => "Ticket successfully created"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Event_ticket  $event_ticket
     * @return \Illuminate\Http\Response
     */
    public function show(Event_ticket $event_ticket)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Event_ticket  $event_ticket
     * @return \Illuminate\Http\Response
     */
    public function edit(Event_ticket $event_ticket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Event_ticket  $event_ticket
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event_ticket $event_ticket)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Event_ticket  $event_ticket
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event_ticket $event_ticket)
    {
        //
    }
}
