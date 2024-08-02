<?php

namespace App\Http\Requests;

use App\Models\Grade;
use App\Models\StudentReport;
use App\Models\StudentReportSemester;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

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
            if (!$this->report) {
                throw new HttpResponseException(response()->json([
                    'status' => 'error',
                    'message' => 'Student report not found.',
                ], 404));
            }
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

    public function getReportForTeacher()
    {
        if (!$this->report) {
            $user = $this->user();
            $this->report = StudentReport::where('teacher_id', $user->id)
                ->where('grade_id', $this->route('gradeId'))
                ->where('student_id', $this->route('studentId'))
                ->with('media')
                ->get();
        }
        return $this->report;
    }

    public function getReportBySemesterTeacher()
    {
        if (!$this->report) {
            $this->report = StudentReport::where('grade_id', $this->route('gradeId'))
                ->where('student_id', $this->route('studentId'))
                ->where('semester_id', $this->route('semesterId'))
                ->with(['media', 'semester'])
                ->latest()
                ->get();
        }
        return $this->report;
    }

    public function getReportForStudent()
    {
        if (!$this->report) {
            $user = $this->user();
            $this->report = StudentReport::where('grade_id', $this->route('gradeId'))
                ->where('student_id', $user->id)
                ->with('media')
                ->latest()
                ->get();
        }
        return $this->report;
    }

    public function getReportBySemesterStudent()
    {
        if (!$this->report) {
            $user = $this->user();
            $this->report = StudentReport::where('grade_id', $this->route('gradeId'))
                ->where('student_id', $user->id)
                ->where('semester_id',  $this->route('semesterId'))
                ->with(['media', 'semester'])
                ->latest()
                ->get();
        }
        return $this->report;
    }
}
