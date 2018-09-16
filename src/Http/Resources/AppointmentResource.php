<?php

namespace Jam0r85\NovaCalendar\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'start' => $this->start->toIso8601String(),
            'end' => optional($this->end)->toIso8601String(),
            'allDay' => $this->all_day,
            'title' => $this->title,
            'description' => $this->description,
            'className' => [
                'cursor-pointer'
            ]
        ];
    }
}
