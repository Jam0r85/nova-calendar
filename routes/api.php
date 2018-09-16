<?php

use Illuminate\Support\Facades\Route;
use Jam0r85\NovaCalendar\Http\Controllers\AppointmentController;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. They are protected
| by your tool's "Authorize" middleware by default. Now, go build!
|
*/

Route::post('/store', AppointmentController::class.'@store');
Route::get('/general', AppointmentController::class.'@general');
Route::post('/show', AppointmentController::class.'@show');
Route::post('/delete', AppointmentController::class.'@destroy');
