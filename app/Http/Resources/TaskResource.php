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
            'grade' => new GradeResource($this->grade),
            'teacher' => new UserResource($this->teacher),
            'media' => TaskMediaResource::collection($this->media),
            'links' => TaskLinkResource::collection($this->links),
        ];
    }
}
