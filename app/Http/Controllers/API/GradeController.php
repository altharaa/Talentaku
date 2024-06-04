<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GradeController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $roles = $user->roles->pluck('name')->toArray(); 

        if (in_array('Murid SD', $roles) || in_array('Murid KB', $roles)) {
            $grades = $user->members()->with('grade')->get()->pluck('grade.name')->toArray();
        } elseif (in_array('Guru SD', $roles) || in_array('Guru KB', $roles)) {
            $grades = Grade::where('teacher_id', $user->id)->pluck('name')->toArray();
        } else {
            return response()->json([
                'message' => 'User doesn\'t have a valid role'
            ], 404);
        }

        if (empty($grades)) {
            return response()->json([
                'message' => 'User has no associated grades'
            ], 404);
        }

        return response()->json([
            'grades' => $grades
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'desc' => 'required|string',
            'level' => 'required|in:SD,KB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $roles = $request->user()->roles()->pluck('name')->toArray();

        if (!in_array('Guru SD', $roles) && !in_array('Guru KB', $roles)){
            return response()->json([
                'status' => 'error',
                'message' => 'Only "Guru SD" or "Guru KB" can create grades.',
            ], 403);
        }

        $grade = new Grade();
        $grade->name = $request->name;
        $grade->desc = $request->desc;
        $grade->level = $request->level;
        $grade->unique_code = Str::random(5);
        $grade->teacher_id = $request->user()->id; 
        $grade->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Grade created successfully.',
            'data' => $grade,
        ], 201); 
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'desc' => 'required|string',
            'level' => 'required|in:SD,KB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $grade = Grade::findOrFail($id);

        $roles = $request->user()->roles()->pluck('name')->toArray();
        if (!in_array('Guru SD', $roles) && !in_array('Guru KB', $roles)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Only "Guru SD" or "Guru KB" can update grades.',
            ], 403);
        }

        $grade->name = $request->name;
        $grade->desc = $request->desc;
        $grade->level = $request->level;
        $grade->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Grade updated successfully.',
            'data' => $grade,
        ], 200);
    }

    public function join(Request $request)
    {
        $request->validate([
            'unique_code' => 'required|string|size:5',
        ]);

        $grade = Grade::with('teacher', 'members')->where('unique_code', $request->unique_code)->first();

        if (!$grade) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid unique code. Class not found.',
            ], 404);
        }

        $grade->members()->attach($request->user()->id);
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

    public function toggleActive(Request $request, $id)
    {
        $grade = Grade::findOrFail($id);

        $roles = $request->user()->roles()->pluck('name')->toArray();
        if (!in_array('Guru SD', $roles) && !in_array('Guru KB', $roles)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Only "Guru SD" or "Guru KB" can toggle grade status.',
            ], 403);
        }

        $grade->isactive = !$grade->isactive;
        $grade->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Grade status updated successfully.',
            'data' => $grade,
        ], 200);
    }
}
