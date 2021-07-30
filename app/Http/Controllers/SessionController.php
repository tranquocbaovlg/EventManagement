<?php

namespace App\Http\Controllers;

use App\Session;
use Illuminate\Http\Request;
use App\Event;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
        if(Auth::user()->id != $event->organizer->id) abort(404);
        return view("session.create", compact("event"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Event $event, Request $request)
    {
        //type title speaker room cost start end description /Title is required.
        if(Auth::user()->id != $event->organizer->id) abort(404);

        $data = $request->validate([
            "type"          => "required",
            "title"         => "required",
            "speaker"       => "required",
            "room_id"       => "required",
            "cost"          => "min:0",
            "start"         => "required|date|date_format:Y-m-d H:i",
            "end"           => "required|date|date_format:Y-m-d H:i|after:start",
            "description"   => "required",
        ], [
            "type.required"         => "Type is required.",
            "title.required"        => "Title is required.",
            "speaker.required"      => "Speaker is required.",
            "room_id.required"      => "Room is required.",
            "start.required"        => "Start is required.",
            "end.required"          => "End is required.",
            "description.required"  => "Description is required.",
        ]);

        $get_session = $event->room()->get()
                            ->where('id', $request->room_id)->first()
                            ->session()->get();
        $room_booked = $get_session->whereBetween('start', [$request->start, $request->end])
                        ??$get_session->WhereBetween('end', [$request->start, $request->end]);
        if($room_booked->first() != null) {
            return back()->withErrors([
                "room" => "Room already booked during this time"
            ])->withInput();
        }

        Session::first()->create($data);

        return redirect()->route("event.show", $event->id)->with([
            "message" => "Session successfully created"
        ]);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Session  $session
     * @return \Illuminate\Http\Response
     */
    public function show(Session $session)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Session  $session
     * @return \Illuminate\Http\Response
     */
    public function edit(Event $event, Session $session)
    {
        if($event->organizer->id != Auth::user()->id) abort(404);
        if($session->room->channel->event->id != $event->id) abort(404);
        if($session->room->channel->event->organizer->id != Auth::user()->id) abort(404);

        return view('session.edit', compact(['event', 'session']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Session  $session
     * @return \Illuminate\Http\Response
     */
    public function update(Event $event, Request $request, Session $session)
    {
        if($event->organizer->id != Auth::user()->id) abort(404);
        if($session->room->channel->event->id != $event->id) abort(404);
        if($session->room->channel->event->organizer->id != Auth::user()->id) abort(404);
        //type title speaker room cost start end description /Title is required.

        $data = $request->validate([
            "type"          => "required",
            "title"         => "required",
            "speaker"       => "required",
            "room_id"       => "required",
            "cost"          => "min:0",
            "start"         => "required|date|date_format:Y-m-d H:i",
            "end"           => "required|date|date_format:Y-m-d H:i|after:start",
            "description"   => "required",
        ], [
            "type.required"         => "Type is required.",
            "title.required"        => "Title is required.",
            "speaker.required"      => "Speaker is required.",
            "room_id.required"      => "Room is required.",
            "start.required"        => "Start is required.",
            "end.required"          => "End is required.",
            "description.required"  => "Description is required.",
        ]);

        $get_session = $event->room()->get()
            ->where('id', $request->room_id)->first()
            ->session()->get()
            ->where('id', '<>', $session->id);
        $room_booked = $get_session->whereBetween('start', [$request->start, $request->end])
            ??$get_session->WhereBetween('end', [$request->start, $request->end]);
        if($room_booked->first() != null) {
            return back()->withErrors([
                "room" => "Room already booked during this time"
            ])->withInput();
        }

        $session->update($data);

        return redirect()->route('event.show', $event->id)->with([
            "message" => "Session successfully updated"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Session  $session
     * @return \Illuminate\Http\Response
     */
    public function destroy(Session $session)
    {
        //
    }
}
