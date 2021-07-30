<?php

namespace App\Http\Controllers;

use App\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;

class EventController extends Controller
{

    public function __construct()
    {
        $this->middleware("auth");
    }

    public function report(Event $event)
    {
        if ($event->organizer->id != Auth::user()->id) abort(404);

        $sessions = collect([]);
        foreach ($event->channel as $channel) {
            foreach ($channel->session as $session) {
                $sessions->push($session);
            }
        }

        $red = 'rgb(240, 170, 160)';
        $green = 'rgb(200, 220, 170)';
        $blue = 'rgb(160, 200, 245)';

        $chart = ([
            'title' => collect(),
            'attendees' => collect(),
            'capacity' => collect(),
            'attendees_color' => collect(),
        ]);

        foreach ($sessions as $session){
            $chart['title']->push($session->title);
            $chart['attendees']->push($session->session_registration->count());
            $chart['capacity']->push($session->room->capacity);
            if($session->session_registration->count() > $session->room->capacity){
                $chart['attendees_color']->push($red);
            } else $chart['attendees_color']->push($green);
        }

        $event['chart'] = $chart;

        return view("report.index", compact("event"));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $eventList = Event::where("organizer_id", Auth::user()->id)->get()->sortBy('date');
        foreach ($eventList as $event){
            $event->registrations = $event->registration->count();
        }
        return view("event.index", compact("eventList"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("event.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            "name" => "required",
            "slug" => "required|regex:/^[a-z0-9\-]+$/",
            "date" => "required|date|date_format:Y-m-d|after:tomorrow"
        ], [
            "name.required" => "Name is required.",
            "slug.required" => "Slug must not be empty and only contain a-z, 0-9 and '-'",
            "slug.regex" => "Slug must not be empty and only contain a-z, 0-9 and '-'",
            "date.required" => "Date is required.",
            "date.date_format" => "Date format must be yyyy-mm-dd",
            "date.after" => "Date must be in the future"
        ]);

        $slugUsed = Event::all()->where("slug", $data["slug"])->first();
        if($slugUsed != null){
            return back()->withErrors([
                "slug" => "Slug is already used"
            ])->withInput();
        }

        $newEvent = Auth::user()->event()->create($data);

        return redirect()->route("event.show", $newEvent->id)->with([
            "message" => "Event successfully created"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event)
    {
        if ($event->organizer->id != Auth::user()->id){
            abort(404);
        }

        return view("event.show", compact("event"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function edit(Event $event)
    {
        if ($event->organizer->id != Auth::user()->id) abort(404);
        return view("event.edit", compact("event"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event $event)
    {
        $data = $request->validate([
            "name" => "required",
            "slug" => "required|regex:/^[a-z0-9\-]+$/",
            "date" => "required|date|date_format:Y-m-d|after:tomorrow"
        ], [
            "name.required" => "Name is required.",
            "slug.required" => "Slug must not be empty and only contain a-z, 0-9 and '-'",
            "slug.regex" => "Slug must not be empty and only contain a-z, 0-9 and '-'",
            "date.required" => "Date is required.",
            "date.date_format" => "Date format must be yyyy-mm-dd",
            "date.after" => "Date must be in the future"
        ]);

        $slugUsed = Event::all()->where("id", "<>", $event->id)->where("slug", $data["slug"])->first();
        if($slugUsed != null){
            return back()->withErrors([
                "slug" => "Slug is already used"
            ])->withInput();
        }

        if ($event->organizer->id != Auth::user()->id){
            abort(404);
        }

        $event->update($data);

        return redirect()->route("event.show", $event->id)->with([
            "message" => "Event successfully updated"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event)
    {
        //
    }
}
