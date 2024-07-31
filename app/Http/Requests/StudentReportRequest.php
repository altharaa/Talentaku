<?php

namespace App\Http\Requests;

use App\Models\Grade;
use App\Models\StudentReport;
use App\Models\StudentReportSemester;
use Illuminate\Foundation\Http\FormRequest;

class StudentReportRequest extends FormRequest
{
    protected $report;

    protected $semester;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $gradeId = $this->route('gradeId');

        if (!$gradeId) {
            return true;
        }

        $grade = Grade::find($gradeId);

        if (!$grade) {
            return false;
        }

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

    public function getSemester()
    {
        if(!$this->semester) {
            $this->semester = StudentReportSemester::all();
        }
        return $this->semester;
    }
}
