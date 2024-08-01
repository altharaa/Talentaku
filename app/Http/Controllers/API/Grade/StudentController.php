<?php

namespace App\Http\Controllers\API\Grade;

use App\Http\Controllers\Controller;
use App\Http\Resources\GradeResource;
use App\Models\Grade;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $grades = $user->grades()->with(['teacher:id,name'])->get();



        return GradeResource::collection($grades);
    }

}
