<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
         return [
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,
            'due_date'    => $this->due_date,
            'is_completed'=> $this->is_completed,
            'priority'    => $this->priority,
        ];
    }
}
