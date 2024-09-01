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

    public function getCompletionsScoreNull()
    {
        if (!$this->completions) {
            $this->completions = TaskSubmission::where('task_id', $this->route('taskId'))
                ->whereNull('score')
                ->with(['student:id,name', 'task', 'task.grade'])
                ->latest()
                ->get();
        }
        if ($this->completions->isEmpty()) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException('Tidak ada tugas yang belum dinilai');
        }
        return $this->completions;
    }

    public function getCompletionsById()
    {
        if (!$this->completions) {
            $this->completions = TaskSubmission::where('task_id', $this->route('taskId'))
                ->where('id', $this->route('submissionId'))
                ->with(['student:id,name', 'task', 'task.grade'])
                ->get();
        }
        return $this->completions;
    }

    public function getCompletionsWithScores()
    {
        if(!$this->completions){
            $this->completions = TaskSubmission::where('task_id', $this->route('taskId'))
                ->whereNotNull('score')
                ->with(['student:id,name', 'task', 'task.grade'])
                ->latest()
                ->get();
        }
        if ($this->completions->isEmpty()) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException('Belum ada tugas yang sudah dinilai');
        }
        return $this->completions;
    }
}
