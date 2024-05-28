<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\User;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $gradeId)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
        ]);

        $student = User::find($request->student_id);

        if ($student && ($student->role == 'murid sd' || $student->role == 'murid tk')) {
            $grade = Grade::find($gradeId);
            if ($grade) {
                $grade->students()->attach($student->id);
                return response()->json(['message' => 'Student added to grade successfully'], 201);
            } else {
                return response()->json(['message' => 'Grade not found'], 404);
            }
        } else {
            return response()->json(['message' => 'User must be a murid sd or murid tk'], 403);
        }
    
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
