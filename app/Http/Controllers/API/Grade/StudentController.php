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

    public function join(Request $request)
    {
        $request->validate([
            'unique_code' => 'required|string|size:5',
        ]);

        $user = $request->user();
        $roles = $user->roles()->pluck('name')->toArray();

        if (!in_array('Murid SD', $roles) && !in_array('Murid KB', $roles)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Only students Murid SD or Murid KB can join a class.',
            ], 403);
        }

        $grade = Grade::with('teacher', 'members')->where('unique_code', $request->unique_code)->first();

        if (!$grade) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid unique code. Class not found.',
            ], 404);
        }

        if (!$grade->isactive) {
            return response()->json([
                'status' => 'error',
                'message' => 'The class is not active. You cannot join an inactive class.',
            ], 403);
        }

        if ($grade->members->contains($user->id)) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are already a member of this class.',
            ], 400);
        }

        $grade->members()->attach($request->user()->id, ['created_at' => now(), 'updated_at' => now()]);
        $grade->load('members');

        $data = [
            'grade_id' => $grade->id,
            'grade_name' => $grade->name,
            'teacher_id' => $grade->teacher ? $grade->teacher->id : null,
            'teacher_name' => $grade->teacher ? $grade->teacher->name : null,
            'students' => $grade->members->map(function ($member) {
                return [
                    'student_id' => $member->id,
                    'student_name' => $member->name,
                ];
            }),
        ];

        return response()->json([
            'status' => 'success',
            'message' => 'Joined grade successfully.',
            'data' => $data,
        ], 200);
    }
}
