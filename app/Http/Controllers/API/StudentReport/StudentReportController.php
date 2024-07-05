<?php

namespace App\Http\Controllers\API\StudentReport;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use App\Models\StudentReportPoint;
use App\Models\StudentReportSemester;
use Illuminate\Http\Request;

class StudentReportController extends Controller
{
    public function semesters()
    {
        $semester = StudentReportSemester::all();

        return response()->json([
            'status' => 'success',
            'data' => $semester,
        ]);
    }

}
