<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
        if ($this->resource instanceof \Illuminate\Database\Eloquent\Collection) {
            return $this->resource->map(function ($submission){
                return $this->formatSubmission($submission);
            })->toArray();
        }

        return $this->formatSubmission($this->resource);
    }

    /**
     * Format a single submission.
     *
     * @param \App\Models\TaskSubmission $submission
     * @param Request $request
     * @return array
     */
    protected function formatSubmission($submission)
    {
        $task = $submission->task;
        $submissionDate = Carbon::parse($submission->created_at);
        
        $isLate = false;
        if ($task && $task->end_date) {
            $isLate = $submissionDate->gt(Carbon::parse($task->end_date));
        }

        return [
            'id' => $submission->id,
            'task_id' => $submission->task_id,
            'student_submitted' => $submission->student ,
            'submmision_media' => $submission->media ? TaskSubmissionMediaResource::collection($submission->media) : [],
            'submitted_at' => $submissionDate->toDateString(),
            'is_late' => $isLate,
            'status' => $isLate ? 'Late' : 'On Time',
            'score' => $submission->score,
        ];

    }
}