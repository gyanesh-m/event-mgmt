<?php

use Illuminate\Http\Request;

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

//Route::get('/', function () {
//    return view('welcome');
//});
//Auth::routes();
//Route::resource('/events','EventsController');
//Route::resource('/user','UserController');
Route::post('/user','UserController@store');
Route::patch('/user','UserController@update');
Route::get('/user','UserController@show');
Route::delete('/user','UserController@destroy');


Route::post('/event','EventsController@store');
Route::patch('/event','EventsController@update');
Route::get('/event','EventsController@show');
Route::delete("/event", "EventsController@destroy");


Route::post('/invite','InviteController@store');
Route::patch('/invite','InviteController@update');
Route::get('/invite','InviteController@show');
Route::delete('/invite','InviteController@destroy');
//Route::get('/event','EventsController@show');