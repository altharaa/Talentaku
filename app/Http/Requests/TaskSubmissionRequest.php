<?php

namespace App\Http\Requests;

use App\Models\Grade;
use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;

class TaskSubmissionRequest extends FormRequest
{
    protected $task;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $grade = Grade::findOrFail($this->route('gradeId'));
        $task = Task::where('id', $this->route('taskId'))
            ->where('grade_id', $grade->id)
            ->firstOrFail();
        $roles = $user->roles()->pluck('name')->toArray();

        return (in_array('Murid SD', $roles) || in_array('Murid KB', $roles))
            && $grade->members()->where('users.id', $user->id)->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'media' => 'nullable|array',
            'media.*' => 'file|mimes:jpg,jpeg,png,mp4,avi,mov,pdf,doc,docx,xls,xlsx,ppt,pptx|max:2048'
        ];
    }

    public function getTask()
    {
        if(!$this->task)
        {
            $this->task = Task::where('id', $this->route('taskId'))
                ->where('grade_id', $this->route('gradeId'))
                ->firstOrFail();
        }
        return $this->task;
    }
}
