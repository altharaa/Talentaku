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
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'desc' => explode(PHP_EOL, $this->desc),
            'grade' => [
                'id' => optional($this->grade)->id,
                'name' => optional($this->grade)->name,
                'is_active' => $this->isactive ? 1 : 0,
                'is_active_status' => $this->isactive ? 'active' : 'inactive',
            ],
            'teacher' => [
                'id' => optional($this->teacher)->id,
                'name' => optional($this->teacher)->name,
            ],
            'media' => TaskMediaResource::collection($this->media),
            'links' => TaskLinkResource::collection($this->links),
        ];
    }
}
