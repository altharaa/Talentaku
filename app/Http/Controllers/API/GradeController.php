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
            $grades = $user->members()->with(['grade', 'grade.teacher', 'grade.members'])->get()->pluck('grade')->unique()->values();     
        } elseif (in_array('Guru SD', $roles) || in_array('Guru KB', $roles)) {
            $grades = Grade::where('teacher_id', $user->id)->with('teacher', 'members')->get();
        } else {
            return response()->json([
                'message' => 'User not authenticated'
            ], 404);
        }

        if (empty($grades)) {
            return response()->json([
                'message' => 'User has no associated grades'
            ], 404);
        }

        $formattedGrades = [];
        foreach ($grades as $grade) {
            $teacherName = optional($grade->teacher)->name;
            $members = $grade->members->map(function($member) {
                return [
                    'id' => $member->id,
                    'name' => $member->name,
                    'photo' => $member->photo,
                ];
            })->toArray();

            $formattedGrade = [
                'name' => $grade->name,
                'desc' => $grade->desc,
                'isactive' => $grade->isactive ? 'active' : 'inactive',
                'teacher' => $teacherName,
                'members' => $members,
            ];

            $formattedGrades[] = $formattedGrade;
        }

        return response()->json([
            'grades' => $formattedGrades
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

        if (!$grade->isactive) {
            return response()->json([
                'status' => 'error',
                'message' => 'The class is not active. You cannot update an inactive class.',
            ], 403);
        }

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

        $user = $request->user();
        $roles = $user->roles()->pluck('name')->toArray();

        if (!in_array('Murid SD', $roles) && !in_array('Murid KB', $roles)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Only students (Murid SD or Murid KB) can join a class.',
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

    public function detail(Request $request, $id) {
        $user = $request->user();
        $grade = Grade::with(['teacher', 'members'])->find($id);

        if (!$grade) {
            return response()->json([
                'status' => 'error',
                'message' => 'Grade not found.',
            ], 404);
        }

        $isTeacher = $grade->teacher_id === $user->id;
        $isMember = $grade->members->contains('id', $user->id);
    
        if (!$isTeacher && !$isMember) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have permission to view this grade.',
            ], 403);
        }

        $gradeDetails = [
            'id' => $grade->id,
            'name' => $grade->name,
            'desc' => $grade->desc,
            'level' => $grade->level,
            'unique_code' => $grade->unique_code,
            'isactive' => $grade->isactive ? 'active' : 'inactive',
            'teacher' => $grade->teacher ? [
                'id' => $grade->teacher->id,
                'name' => $grade->teacher->name,
                'roles' => $grade->teacher->roles()->pluck('name')->toArray()
            ] : null,
            'members' => $grade->members->map(function($member) {
                return [
                    'id' => $member->id,
                    'name' => $member->name,
                    'photo' => $member->photo,
                    'roles' => $member->roles()->pluck('name')
                ];
            }),
            'created_at' => $grade->created_at
        ];

        return response()->json([
            'status' => 'success',
            'data' => $gradeDetails,
        ], 200);
    }

    public function deleteMember(Request $request, $gradeId, $memberId)
    {
        $grade = Grade::findOrFail($gradeId);

        $roles = $request->user()->roles()->pluck('name')->toArray();
        if (!in_array('Guru SD', $roles) && !in_array('Guru KB', $roles)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Only "Guru SD" or "Guru KB" can delete members from a grade.',
            ], 403);
        }

        if ($grade->teacher_id !== $request->user()->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'You can only delete members from grades you teach.',
            ], 403);
        }

        if (!$grade->members()->where('users.id', $memberId)->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'The specified member is not in this grade.',
            ], 404);
        }

        $grade->members()->detach($memberId);

        return response()->json([
            'status' => 'success',
            'message' => 'Member removed successfully.',
        ], 200);
    }
}
