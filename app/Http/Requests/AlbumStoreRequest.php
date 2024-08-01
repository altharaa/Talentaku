<?php

namespace App\Http\Requests;

use App\Models\Grade;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AlbumStoreRequest extends FormRequest
{
    protected $grade;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        $user = $this->user();
        $gradeId = $this->route('gradeId');

        try {
            $this->grade = Grade::findOrFail($gradeId);
        } catch (ModelNotFoundException $e) {
            throw new HttpResponseException(response()->json([
                'status' => 'error',
                'message' => 'Grade not found.',
            ], 404));
        }

        $roles = $user->roles()->pluck('name')->toArray();

        if ($this->grade->isactive == 0) {  
            throw new HttpResponseException(response()->json([
                'status' => 'error',
                'message' => 'Cannot create task. The associated grade is not active.',
            ], 403));
        }

        return (in_array('Guru SD', $roles) || in_array('Guru KB', $roles))
            && $user->id == $this->grade->teacher_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'desc' => 'string',
            'media' => 'required|array',
            'media.*' => 'file|mimes:jpeg,png,jpg,gif,svg,mp4,mov,avi|max:20480',
        ];
    }

    public function messages()
    {
        return [
            'media.required' => 'At least one media file is required.',
            'media.*.file' => 'Each media item must be a file.',
            'media.*.mimes' => 'Only jpeg, png, jpg, gif, svg, mp4, mov, and avi files are allowed.',
            'media.*.max' => 'Each file must not exceed 20MB in size.',
        ];
    }
}
