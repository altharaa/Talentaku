<?php

namespace App\Http\Controllers\API\StudentReport;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\StudentReport;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function display(Request $request, $gradeId) {
        $user = $request->user();
        $grade = Grade::find($gradeId);

        if (!$grade) {
            return response()->json([
                'status' => 'error',
                'message' => 'Grade not found.',
            ], 404);
        }
         $studentReports = StudentReport::where('grade_id', $gradeId)->where('student_id', $user->id)->with('media')->get();

        return response()->json([
            'status' => 'success',
            'data' => $studentReports
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

    public function displayStudentReportsBySemester(Request $request, $gradeId, $studentId, $semester)
    {
       
    }
}
