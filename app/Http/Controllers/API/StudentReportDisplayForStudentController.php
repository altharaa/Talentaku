<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentReportRequest;
use App\Http\Resources\StudentReportResource;
use App\Models\Grade;
use App\Models\StudentReport;
use Illuminate\Http\Request;

class StudentReportDisplayForStudentController extends Controller
{
    public function displayAll(StudentReportRequest $request) {
        $studentReports = $request->getReportForStudent();
        return StudentReportResource::collection($studentReports);
    }

    public function displayBySemester(StudentReportRequest $request)
    {
        $studentReports = $request->getReportBySemesterStudent();
        return StudentReportResource::collection($studentReports);
    }
}
