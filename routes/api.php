<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group(["prefix" => "v1"], function (){
    Route::get("events", "Api\ApiController@events");
    Route::group(["prefix" => "organizers/{organizer_slug}/events/{event_slug}"], function (){
        Route::get('/', "Api\ApiController@organizers");
        Route::post('registration', "Api\ApiController@registration");
    });
    Route::post("login", "Api\ApiController@login");
    Route::post("logout", "Api\ApiController@logout");
    Route::get("registrations", "Api\ApiController@registrations");
});

