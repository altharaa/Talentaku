<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentReportRequest;
use App\Http\Resources\StudentReportResource;
use Carbon\Carbon;

class StudentReportDisplayForTeacherController extends Controller
{
    public function displayAll(StudentReportRequest $request) {
        try {
            $studentReports = $request->getReportForStudent();
            $formattedReports = $studentReports->map(function ($reports, $yearMonth) {
                return [
                    'month' => Carbon::parse($yearMonth)->format('F Y'),
                    'reports' => StudentReportResource::collection($reports)
                ];
            })->values();
    
            return response()->json($formattedReports);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function displayBySemester(StudentReportRequest $request)
    {
        try {
            $studentReports = $request->getReportBySemesterTeacher();
            $formattedReports = $studentReports->map(function ($reports, $yearMonth) {
                return [
                    'month' => Carbon::parse($yearMonth)->format('F Y'),
                    'reports' => StudentReportResource::collection($reports)
                ];
            })->values();
    
            return response()->json($formattedReports);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
}
