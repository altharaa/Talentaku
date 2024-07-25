<?php

namespace App\Http\Requests;

use App\Models\Grade;
use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;

class TaskShowByIdRequest extends FormRequest
{
    protected $task;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $grade = Grade::findOrFail($this->route('gradeId'));
        
        return $user->id == $grade->teacher_id || $grade->members->contains($user->id);
    
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

    public function getTask()
    {
        if (!$this->task) {
            $this->task = Task::where('id', $this->route('taskId'))
                ->where('grade_id', $this->route('gradeId'))
                ->with(['media', 'links', 'grade.teacher'])
                ->firstOrFail();
        }

        return $this->task;
    }
}
