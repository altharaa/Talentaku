<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\GradeDeleteRequest;
use App\Http\Requests\GradeStoreRequest;
use App\Http\Requests\GradeUpdateRequest;
use App\Http\Resources\GradeResource;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GradeController extends Controller
{
    public function store(GradeStoreRequest $request)
    {
        try {
            $user = $request->user();
            $grade = Grade::create([
                'name' => $request->name,
                'desc' => $request->desc,
                'level_id' => $request->level_id,
                'unique_code' => Str::random(5),
                'teacher_id' => $user->id,
            ]);
            $grade->load('level');
            return $this->resStoreData(new GradeResource($grade));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resError($e->getMessage(), 500);
        }
    }


    public function update(GradeUpdateRequest $request)
    {
        try {
            $grade = $request->getGrade();
            $grade->update($request->only(['name', 'desc', 'level_id']));
            $grade->load('level');
            return $this->resUpdateData(new GradeResource($grade));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resError($e->getMessage(), 500);
        }
    }


    public function delete(GradeDeleteRequest $request)
    {
        try {
            $grade = $request->getGrade();
            $grade->delete();
            return $this->resDeleteData('Grade deleted succesfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resError($e->getMessage(), 500);
        }
    }
}
