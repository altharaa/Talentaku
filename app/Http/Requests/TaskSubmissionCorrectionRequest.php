<?php

namespace App\Http\Requests;

use App\Models\Grade;
use App\Models\Task;
use App\Models\TaskSubmission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class TaskSubmissionCorrectionRequest extends FormRequest
{
    protected $taskSubmission;

    protected $gradeId;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $grade = Grade::findOrFail($this->route('gradeId'));
        if ($this->user()->id != $grade->teacher_id) {
            throw new HttpResponseException(response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to grade submissions for this class.',
            ], 403));
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'score' => 'required',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->taskSubmission = TaskSubmission::find($this->route('submissionId'));
            if (!$this->taskSubmission) {
                $validator->errors()->add('task_submission_id', 'The task submission does not exist.');
            }
        });
    }

    /**
     * Get the validated task submission.
     *
     * @return TaskSubmission
     */
    public function getTaskSubmission(): TaskSubmission
    {
        $taskId = $this->route('taskId');
        $task = Task::where('id', $taskId)->where('grade_id', $this->route('gradeId'))->first();
        if (!$task) {
            throw new HttpResponseException(response()->json([
                'status' => 'error',
                'message' => 'Task not found in the specified grade.',
            ], 404));
        }

        $submissionId = $this->route('submissionId');
        $submission = TaskSubmission::where('id', $submissionId)
            ->where('task_id', $taskId)
            ->first();

        if (!$submission) {
            throw new HttpResponseException(response()->json([
                'status' => 'error',
                'message' => 'Submission not found.',
            ], 404));
        }

        return $this->taskSubmission;
    }
}
