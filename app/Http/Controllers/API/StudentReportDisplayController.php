<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentReportRequest;
use App\Http\Resources\StudentReportResource;
use App\Models\Grade;
use App\Models\StudentReport;
use Illuminate\Http\Request;

class StudentReportDisplayController extends Controller
{
    public function detail(StudentReportRequest $request)
    {
        $studentReport = $request->getReportDetail();
        return new StudentReportResource($studentReport);
    }
}
