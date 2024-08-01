<?php

namespace App\Http\Requests;

use App\Models\Album;
use App\Models\Grade;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AlbumShowByIdRequest extends FormRequest
{
    protected $grade;
    protected $album;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        try {
            $user = $this->user();
            $this->grade = Grade::findOrFail($this->route('gradeId'));
            $this->album = Album::where('grade_id', $this->grade->id)
                                ->findOrFail($this->route('albumId'));
            
            $isTeacher = $this->grade->teacher_id == $user->id;
            $isMember = $this->grade->members()->where('users.id', $user->id)->exists();
            
            return $isTeacher || $isMember;
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('The requested album was not found.');
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
        if (!$this->album) {
            return response()->json([
                'status' => 'error',
                'message' => 'Album not found.',
            ], 404);
        }
    
        return response()->json([
            'status' => 'error',
            'message' => 'You are not authorized to view this album.',
        ], 403);
    }

    public function getGrade()
    {
        return $this->grade;
    }

    public function getAlbum()
    {
        if (!$this->album) {
            $this->album = Album::where('grade_id', $this->route('gradeId'))
                                ->findOrFail($this->route('albumId'));
        }
        return $this->album;
    }
}
