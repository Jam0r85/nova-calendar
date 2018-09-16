<?php

namespace Jam0r85\NovaCalendar\Http\Controllers;

use App\Appointment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Jam0r85\NovaCalendar\Http\Resources\AppointmentResource;
use Laravel\Nova\Http\Requests\NovaRequest;

class AppointmentController extends Controller
{
    public function show(NovaRequest $request)
    {
        return new AppointmentResource(Appointment::find($request->appointment_id));
    }

    public function store(NovaRequest $request)
    {
        $request->validate([
            'start' => 'required',
            'end' => '|nullable',
            'title' => 'required|string'
        ]);
        
        try {
            if ($request->has('appointment_id') && $request->appointment_id) {
                $appointment = Appointment::find($request->appointment_id);
            } else {
                $appointment = new Appointment();
            }

            $appointment->start = new Carbon($request->start);
            $appointment->end = $request->end ? new Carbon($request->end) : null;
            $appointment->title = $request->title;
            $appointment->description = $request->description;
            $appointment->all_day = $request->all_day;
            $appointment->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Appointment was stored'
            ]);
        } catch (\ErrorException $e) {
            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage()
            ], 503);
        }
    }

    public function general(NovaRequest $request)
    {
        $appointments = Appointment::whereParentType(null)->get();

        return AppointmentResource::collection($appointments);
    }

    public function destroy(NovaRequest $request)
    {
        try {
            $appointment = Appointment::find($request->appointment_id);
            $appointment->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Appointment deleted.'
            ]);
        } catch (\ErrorException $e) {
            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage()
            ], 503);
        }
    }
}
