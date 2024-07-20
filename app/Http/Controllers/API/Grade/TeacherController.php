<?php

namespace App\Http\Controllers\API\Grade;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\GradeLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $roles = $user->roles->pluck('name')->toArray();
        if (in_array('Guru SD', $roles) || in_array('Guru KB', $roles)) {
            $grades = Grade::where('teacher_id', $user->id)->with('teacher', 'members')->get();
        } else {
            return response()->json([
                'message' => 'You are not allowed to perform this action'
            ], 403);  
        }

        if ($grades->isEmpty()) {
            return response()->json([
                'message' => 'User has no associated grades'
            ], 404);
        }

        $formattedGrades = $grades->map(function ($grade) {
            return [
                'id' => $grade->id,
                'name' => $grade->name,
                'desc' => $grade->desc,
                'isactive' => $grade->isactive,
                'teacher' => $grade->teacher ? [
                    'id' => $grade->teacher->id,
                    'name' => $grade->teacher->name,
                ] : null,
                'members' => $grade->members->map(function ($member) {
                    return [
                        'id' => $member->id,
                        'name' => $member->name,
                        'photo' => $member->photo,
                    ];
                }),
            ];
        });

        return response()->json([
            'grades' => $formattedGrades
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'desc' => 'required|string',
            'level_id' => 'required|exists:grade_levels,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();
        $roles = $user->roles()->pluck('name')->toArray();
        $level = GradeLevel::findOrFail($request->level_id);

        $authorizedRoles = [
            'SD' => ['Guru SD'],
            'KB' => ['Guru KB'],
        ];
    
        if (!isset($authorizedRoles[$level->name]) || !array_intersect($roles, $authorizedRoles[$level->name])) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to create a class for this level.',
            ], 403);
        }

        try {
            $grade = Grade::create([
                'name' => $request->name,
                'desc' => $request->desc,
                'level_id' => $request->level_id,
                'unique_code' => Str::random(5),
                'teacher_id' => $user->id,
            ]);

            $grade->load('level');

            return response()->json([
                'status' => 'success',
                'message' => 'Grade created successfully.',
                'data' => $grade,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating grade: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while creating the grade.',
            ], 500);
        } 
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'desc' => 'required|string',
            'level_id' => 'required|exists:grade_levels,id',
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

        $user = $request->user();
        $roles = $user->roles()->pluck('name')->toArray();
        if (!in_array('Guru SD', $roles) && !in_array('Guru KB', $roles)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Only "Guru SD" or "Guru KB" can update grades.',
            ], 403);
        }

        if ($grade->teacher_id != $user->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'You can only update grades you are teaching.',
            ], 403);
        }

        $grade->update($request->only(['name', 'desc', 'level_id']));
        $grade->load('level');

        return response()->json([
            'status' => 'success',
            'message' => 'Grade updated successfully.',
            'data' => $grade,
        ], 200);
    }

    public function toggleActive(Request $request, $id)
    {
        $grade = Grade::findOrFail($id);

        $user = $request->user();
        $roles = $user->roles()->pluck('name')->toArray();
        if (!in_array('Guru SD', $roles) && !in_array('Guru KB', $roles)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Only "Guru SD" or "Guru KB" can toggle grade status.',
            ], 403);
        }

        if ($grade->teacher_id != $user->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'You can only toggle status for grades you are teaching.',
            ], 403);
        }

        $grade->isactive = !$grade->isactive;
        $grade->save();

        $statusMessage = $grade->isactive ? 'activated' : 'deactivated';

        return response()->json([
            'status' => 'success',
            'message' => "Grade has been successfully {$statusMessage}.",
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

        $isTeacher = $grade->teacher_id == $user->id;
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

        $user = $request->user();
        $roles = $user->roles()->pluck('name')->toArray();
        if (!in_array('Guru SD', $roles) && !in_array('Guru KB', $roles)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Only "Guru SD" or "Guru KB" can delete members from a grade.',
            ], 403);
        }

        if ($grade->teacher_id != $user->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'You can only delete members from grades you teach.',
            ], 403);
        }

        $member = $grade->members()->find($memberId);
        if (!$member) {
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
