<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\GradeResource;
use App\Models\Grade;
use Illuminate\Http\Request;

class GradeActiveController extends Controller
{
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

        return new GradeResource($grade);
    }
}
