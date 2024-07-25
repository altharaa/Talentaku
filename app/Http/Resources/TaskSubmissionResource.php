<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskSubmissionResource extends JsonResource
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
            'task_id' => $this->task_id,
            'student_id' => $this->student_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'score' => $this->score,
            'user' => new UserResource($this->whenLoaded('student')),
            'grade' => new GradeResource($this->whenLoaded('task.grade')),
            'task' => new TaskResource($this->whenLoaded('task')),
            'media' => TaskSubmissionMediaResource::collection($this->whenLoaded('media')),
        ];
    }
}
