<?php

namespace App\Http\Controllers\API\StudentReport;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\StudentReport;
use App\Models\StudentReportSemester;
use App\Models\User;
use Illuminate\Http\Request;

class StudentReportController extends Controller
{
    public function show(Request $request, $gradeId, $studentReportId) {
        $user = $request->user();
        $grade = Grade::find($gradeId);

        if (!$grade) {
            return response()->json([
                'status' => 'error',
                'message' => 'Grade not found.',
            ], 404);
        }

        if ($grade->teacher_id != $user->id) {
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
}
