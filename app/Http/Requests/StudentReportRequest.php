<?php

namespace App\Http\Requests;

use App\Models\Grade;
use App\Models\StudentReport;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StudentReportRequest extends FormRequest
{
    protected $grade;
    protected $report;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $gradeId = $this->route('gradeId');
        $this->grade = Grade::findOrFail($gradeId);

        if ($this->grade->isactive == "inactive") {
            throw new HttpResponseException(response()->json([
                'status' => 'error',
                'message' => 'This action is not allowed due to this associated grade being inactive.',
            ], 403));
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

    public function getReport()
    {
        if(!$this->report) {
            $this->report = StudentReport::where('id', $this->route('studentReportId'))
                ->where('grade_id', $this->route('gradeId'))
                ->with(['media', 'grade.teacher'])
                ->firstOrFail();
        }
        return $this->report;
    }
}
