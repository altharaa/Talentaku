<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\GradeDeleteMemberRequest;
use App\Http\Requests\GradeJoinRequest;
use App\Http\Resources\GradeResource;
use Illuminate\Support\Facades\DB;

class GradeMemberController extends Controller
{
    public function join(GradeJoinRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = $request->user();
            $grade = $request->getGrade();
            $grade->members()->attach($user->id, ['created_at' => now(), 'updated_at' => now()]);
            $grade->load('members');

            DB::commit();

            return new GradeResource($grade);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resError($e->getMessage(), 500);
        }
    }

    public function deleteMember(GradeDeleteMemberRequest $request)
    {
        try {
            DB::beginTransaction();

            $grade = $request->getGrade();
            $memberId = $request->route('memberId');

            $grade->members()->detach($memberId);
            $grade->load('members');

            DB::commit();

            return response()->json([
                'message' => 'Member removed successfully',
                'data' => new GradeResource($grade)
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resError($e->getMessage(), 500);
        }
    }
}
