<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class Student extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created' => $this->created_at->toDateTimeString(),
            'subjects' => Subject::collection($this->whenLoaded('subjects'))
        ];
    }
}
