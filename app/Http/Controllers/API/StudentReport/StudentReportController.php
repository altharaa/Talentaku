<?php

namespace App\Http\Controllers\API\StudentReport;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Semester;
use App\Models\StudentReport;
use App\Models\StudentReportPoint;
use App\Models\StudentReportSemester;
use App\Models\User;
use Illuminate\Http\Request;

class StudentReportController extends Controller
{
    public function displaySemesters()
    {
        $semester = StudentReportSemester::all();

        return response()->json([
            'status' => 'success',
            'data' => $semester,
        ]);
    }

    public function show(Request $request, $gradeId, $studentReportId) {
        $user = $request->user();
        $grade = Grade::find($gradeId);

        if (!$grade) {
            return response()->json([
                'status' => 'error',
                'message' => 'Grade not found.',
            ], 404);
        }

        if ($user->id !== $grade->teacher_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to perform this action for the specified grade.',
            ], 403);
        }

        $studentReport = StudentReport::with('media')->find($studentReportId);

        if (!$studentReport) {
            return response()->json([
                'status' => 'error',
                'message' => 'Student report not found.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $studentReport
        ]);
    }

    public function displayTeacher(Request $request, $gradeId, $studentId) {
        $user = $request->user();
        $grade = Grade::findOrFail($gradeId);

        $roles = $user->roles()->pluck('name')->toArray();
        if (!in_array('Guru SD', $roles) || !in_array('Guru KB', $roles)){
            return response()->json([
                'status' => 'error',
                'message' => 'Only teachers (Guru SD or Guru KB) can perform this action.',
            ], 403);
        }

        if ($user->id !== $grade->teacher_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to perform this action for the specified grade.',
            ], 403);
        }

        if (!$grade->members()->where('users.id', $studentId)->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'The specified student is not in this grade.',
            ], 404);
        }

        $studentReports = StudentReport::where('teacher_id', $user->id)
                                    ->where('grade_id', $gradeId)
                                    ->where('student_id', $studentId)
                                    ->with('media')
                                    ->get();

        $reportsBySemester = $studentReports->groupBy('semester_id');

        $formattedData = $reportsBySemester->map(function ($reports) {
            $semester = $reports->first()->semester;
            return [
                'semester' => [
                    'id' => $semester->id,
                    'name' => $semester->name,
                ],
                'reports' => $reports
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $formattedData
        ]);
    }

    public function displayStudentReportsBySemester(Request $request, $gradeId, $studentId, $semesterId)
    {
        $user = $request->user();
        
        $roles = $user->roles()->pluck('name')->toArray();
        if (!in_array('Guru SD', $roles) || !in_array('Guru KB', $roles)){
            return response()->json([
                'status' => 'error',
                'message' => 'Only teachers (Guru SD or Guru KB) can perform this action.',
            ], 403);
        }

        $grade = Grade::findOrFail($gradeId);
        $student = User::findOrFail($studentId);
        $semester = StudentReportSemester::findOrFail($semesterId);

        if ($user->id !== $grade->teacher_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to access information for this grade.',
            ], 403);
        }

        if (!$grade->members()->where('users.id', $student->id)->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'The specified student is not in this grade.',
            ], 404);
        }

        $studentReports = StudentReport::where('grade_id', $gradeId)
            ->where('student_id', $studentId)
            ->where('semester_id', $semesterId)
            ->with(['media', 'semester'])
            ->get();

        $formattedReports = $studentReports->map(function ($report) {
            return [
                'id' => $report->id,
                'created' => $report->created,
                'kegiatan_awal' => $report->kegiatan_awal,
                'awal_point' => $report->awal_point,
                'kegiatan_inti' => $report->kegiatan_inti,
                'inti_point' => $report->inti_point,
                'snack' => $report->snack,
                'snack_point' => $report->snack_point,
                'inklusi' => $report->inklusi,
                'inklusi_point' => $report->inklusi_point,
                'catatan' => $report->catatan,
                'semester' => $report->semester->name,
                'media' => $report->media->map(function ($media) {
                    return [
                        'id' => $media->id,
                        'file_path' => $media->file_path,
                    ];
                }),
            ];
        });

        if ($formattedReports->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'message' => 'No reports found for the specified student, grade, and semester.',
                'data' => [
                    'student' => [
                        'id' => $student->id,
                        'name' => $student->name,
                    ],
                    'grade' => [
                        'id' => $grade->id,
                        'name' => $grade->name,
                    ],
                    'semester' => [
                        'id' => $semester->id,
                        'name' => $semester->name,
                    ],
                    'reports' => [],
                ],
            ], 200);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name,
                ],
                'grade' => [
                    'id' => $grade->id,
                    'name' => $grade->name,
                ],
                'semester' => [
                    'id' => $semester->id,
                    'name' => $semester->name,
                ],
                'reports' => $formattedReports,
            ],
        ], 200);
    }
}
