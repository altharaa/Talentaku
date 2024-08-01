<?php

namespace App\Http\Requests;

use App\Models\Grade;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AlbumShowByGradeRequest extends FormRequest
{
    protected $grade;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {    
        try {
            $user = $this->user();
            $this->grade = Grade::findOrFail($this->route('gradeId'));
            $isTeacher = $this->grade->teacher_id == $user->id;
            $isMember = $this->grade->members()->where('users.id', $user->id)->exists();
            return $isTeacher || $isMember;
        } catch (ModelNotFoundException $e) {
            return false;
        }
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

    public function failedAuthorization()
    {
        if (!$this->grade) {
            return response()->json([
                'status' => 'error',
                'message' => 'Grade not found.',
            ], 404);
        }

        if (!$this->grade->members->contains($this->user()->id)) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not a member of this grade.',
            ], 403);
        }
        
        return response()->json([
            'status' => 'error',
            'message' => 'You are not authorized to view albums for this grade.',
        ], 403);
    }

    public function getGrade()
    {
        return $this->grade;
    }
}
