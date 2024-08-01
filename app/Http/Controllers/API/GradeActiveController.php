<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\GradeActiveRequest;
use App\Http\Resources\GradeResource;
use App\Models\Grade;
use Illuminate\Http\Request;

class GradeActiveController extends Controller
{
    public function toggleActive(GradeActiveRequest $request)
    {
        $grade = $request->getGrade();
        $grade->isactive = !$grade->isactive;
        $grade->save();

        return new GradeResource($grade);
    }
}
