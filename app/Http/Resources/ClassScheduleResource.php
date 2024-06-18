<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClassScheduleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'day' => $this->day,
            'date' => $this->date,
            'start' => $this->start,
            'time_start' => $this->time_start,
            'time_end' => $this->time_end,
            'start_formatted' => $this->start_formatted,
            'end_formatted' => $this->end_formatted,
            'sold_out' => $this->sold_out,
            'max_pax' => $this->max_pax,
            'current_pax' => $this->current_pax,
            'instructor' => new UserResource($this->instructor),
            'price' => $this->price
        ];
    }
}
