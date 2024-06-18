<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClassSessionResource extends JsonResource
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
            'category' => new ClassCategoryResource($this->category),
            'name' => $this->name,
            'max_pax' => $this->max_pax,
            'instructor' => new UserResource($this->instructor),
            'price' => $this->price,
            'color' => $this->color,
            'schedules' => ClassScheduleResource::collection($this->schedules)
        ];
    }
}
