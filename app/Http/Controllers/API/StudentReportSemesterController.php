<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentReportRequest;

class StudentReportSemesterController extends Controller
{
    public function displaySemesters(StudentReportRequest $request)
    {
        $semester = $request->getSemester();

        return response()->json([
            'status' => 'success',
            'data' => $semester,
        ]);
    }

    public function displayStudentReportsBySemester(StudentReportRequest $request)
    {

    }
}
