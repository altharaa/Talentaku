<?php

namespace App\Http\Requests;

use App\Models\Grade;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StudentReportUpdateRequest extends FormRequest
{
    protected $grade;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
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
                'message' => 'Cannot update student reports. The associated grade is not active.',
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
            'created' => 'required|date',
            'semester_id' => 'required|exists:student_report_semesters,id',
            'kegiatan_awal' => 'required|string',
            'awal_point' => 'required|in:Muncul,Kurang,Belum Muncul',
            'kegiatan_inti' => 'required|string',
            'inti_point' => 'required|in:Muncul,Kurang,Belum Muncul',
            'snack' => 'required|string',
            'snack_point' => 'required|in:Muncul,Kurang,Belum Muncul',
            'inklusi' => 'required|string',
            'inklusi_point' => 'required|in:Muncul,Kurang,Belum Muncul',
            'catatan' => 'required|string',
            'student_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    if (!$this->grade->members()->where('users.id', $value)->exists()) {
                        $fail('The specified student is not in this grade.');
                    }
                },
            ],
            'media' => 'nullable|array',
            'media.*' => 'file|mimes:jpeg,png,jpg,gif,svg,mp4,mov,avi|max:20480',
            'delete_media' => 'nullable|array',
            'delete_media.*' => 'exists:student_report_media,id'
        ];
    }
}
