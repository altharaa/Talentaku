<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentReportRequest;
use App\Http\Resources\StudentReportResource;
use App\Models\Grade;
use App\Models\StudentReport;
use App\Models\StudentReportSemester;
use App\Models\User;
use Illuminate\Http\Request;

class StudentReportDisplayForTeacherController extends Controller
{
    public function displayAll(StudentReportRequest $request) {
        $studentReports = $request->getReportForTeacher();
        return StudentReportResource::collection($studentReports);
    }

    public function displayBySemester(StudentReportRequest $request)
    {
        $studentReports = $request->getReportBySemesterTeacher();
       return StudentReportResource::collection($studentReports);
    }
}
