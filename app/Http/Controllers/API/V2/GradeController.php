<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\GradeMemberResource;
use App\Http\Resources\GradeResource;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GradeController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'desc' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $grade =  Grade::findOrFail($request->route('gradeId'));
            if (!$grade->isactive) {
                return $this->resError('Grade is not active', 400);
            }
    
            $grade->update($request->only(['name', 'desc']));
            $grade->load('level');

            DB::commit();

            return $this->resUpdateData(new GradeResource($grade));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resError($e->getMessage(), 500);
       }
    }

    public function patchActive(Request $request)
    {
        try {
            DB::beginTransaction();

            $grade = Grade::findOrFail($request->route('gradeId'));
            $grade->isactive = !$grade->isactive;
            $grade->save();

            DB::commit();
            return new GradeResource($grade);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resError($e->getMessage(), 500);
        }
    }

    public function getTeacher(Request $request)
    {
        $user = $request->user()->id;
        $grades = Grade::where('teacher_id', $user)
                    ->with( 'teacher', 'members')
                    ->get();       
        return GradeResource::collection($grades);   
    }

    public function getStudent(Request $request)
    {
        $userId = $request->user()->id;
        $grades = Grade::whereHas('members', function ($query) use ($userId) {
            $query->where('users.id', $userId);
        })->get();        
        
        return GradeResource::collection($grades);   
    }

    public function getDetail(Request $request)
    {
        $grade = Grade::with(['teacher', 'members'])->findOrFail($request->route('gradeId'));

        return new GradeMemberResource($grade);
    }
}
