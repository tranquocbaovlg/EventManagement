<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers;


Auth::routes();

Route::get("/", function (){
    return redirect("/event");
});

Route::get("logout", "Auth\LoginController@logout");

Route::resource("event", "EventController");

Route::group(["prefix" => "event/{event}"], function() {
    Route::resource("ticket", "TicketController");
    Route::resource("channel", "ChannelController");
    Route::resource("room", "RoomController");
    Route::resource("session", "SessionController");
    Route::get("report", "EventController@report")->name("report");
});

