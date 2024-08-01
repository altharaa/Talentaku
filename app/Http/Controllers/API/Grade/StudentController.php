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
        $roles = $user->roles->pluck('name')->toArray();

        if (!in_array('Murid SD', $roles) && !in_array('Murid KB', $roles)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Only students Murid SD or Murid KB can view their grades.',
            ], 403);
        }

        $grades = $user->grades()->with(['teacher:id,name'])->get();

        if (empty($grades)) {
            return response()->json([
                'message' => 'User has no associated grades'
            ], 404);
        }

        return GradeResource::collection($grades);
    }

}
