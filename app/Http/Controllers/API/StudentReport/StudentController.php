<?php

namespace App\Http\Controllers\API\StudentReport;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\StudentReport;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function displaySemesters(Request $request, $gradeId) {
        $user = $request->user();
        $grade = Grade::find($gradeId);

        $roles = $user->roles()->pluck('name')->toArray();
        if (!in_array('Murid SD', $roles) && !in_array('Murid KB', $roles)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Only students (Murid SD or Murid KB) can join a class.',
            ], 403);
        }

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

    public function displayStudentReportsBySemester(Request $request, $gradeId, $semester)
    {
        $user = $request->user();
        $grade = Grade::findOrFail($gradeId);

        $roles = $user->roles()->pluck('name')->toArray();
        if (!in_array('Murid SD', $roles) && !in_array('Murid KB', $roles)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Only students (Murid SD or Murid KB) can join a class.',
            ], 403);
        }

        if (!$grade->members()->where('users.id', $user->id)->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not a member of this grade.',
            ], 403);
        }

        $studentReports = StudentReport::where('grade_id', $gradeId)
        ->where('student_id', $user->id)
        ->whereHas('semester', function ($query) use ($semester) {
            $query->where('id', $semester);
        })
        ->with(['media', 'semester'])
        ->get();

        if ($studentReports->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'message' => 'No reports found for the specified semester.',
                'data' => []
            ], 200);
        }

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
    
        return response()->json([
            'status' => 'success',
            'data' => $formattedReports
        ], 200);
    }
}
