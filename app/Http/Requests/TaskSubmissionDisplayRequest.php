<?php

namespace App\Http\Requests;

use App\Models\Grade;
use App\Models\Task;
use App\Models\TaskSubmission;
use Illuminate\Foundation\Http\FormRequest;

class TaskSubmissionDisplayRequest extends FormRequest
{
    protected $completions;
    protected $task;
    protected $grade;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $gradeId = $this->route('gradeId');
        $taskId = $this->route('taskId');

        $this->grade = Grade::find($gradeId);
        $this->task = Task::where('id', $taskId)->where('grade_id', $gradeId)->first();

        if (!$this->grade || !$this->task) {
            return false;
        }

        return $user->id == $this->grade->teacher_id || $this->grade->members->contains($user->id);
      }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }

    public function getCompletions()
    {
        if (!$this->completions) {
            $this->completions = TaskSubmission::where('task_id', $this->route('taskId'))
                ->with(['student:id,name', 'task', 'task.grade'])
                ->get();
        }
        return $this->completions;
    }

    public function getGrade()
    {
        return $this->grade;
    }

    public function getTask()
    {
        return $this->task;
    }
}
