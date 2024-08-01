<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\GradeDisplayRequest;
use App\Http\Resources\GradeResource;

class GradeDisplayController extends Controller
{
    public function getAllGradeTeacher(GradeDisplayRequest $request)
    {
        $grades = $request->getGrades('teacher');
        return GradeResource::collection($grades);
    }

    public function getAllGradeMember(GradeDisplayRequest $request)
    {
        $grades = $request->getGrades('member');
        return GradeResource::collection($grades);
    }

    public function detail(GradeDisplayRequest $request, $gradeId)
    {
        $grade = $request->getGrade($gradeId);
        return new GradeResource($grade);
    }
}
