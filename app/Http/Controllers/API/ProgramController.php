<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProgramController extends Controller
{
    public function show() 
    {
        $programs = Program::all();

        if($programs) {
            return response()->json([
                'message' => 'Programs retrieved successfully',
                'programs' => $programs
            ]);
        } else {
            return response()->json([
                'message' => 'Failed to retrieve programs'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validatedData = $request->all();

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo')->store('public/photos');
            $validatedData['photo'] = basename($photo);
        }

        if ($program = Program::create($validatedData)) {
            return response()->json([
                'message' => 'Program created successfully',
                'program' => $program
            ], 201);
        } else {
            return response()->json([
                'message' => 'Failed to create program'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $program = Program::findOrFail($id);
        $validatedData = $request->all();

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo')->storePublicly('photos', 'public');
            $validatedData['photo'] = Storage::url($photo);
        }

        if ($program->update($validatedData)) {
            return response()->json([
                'message' => 'Program updated successfully',
                'program' => $program
            ]);
        } else {
            return response()->json([
                'message' => 'Failed to update program'
            ], 500);
        }
    }

    public function destroy($id)
    {
        $program = Program::findOrFail($id);
        if ($program->delete()) {
            return response()->json([
                'message' => 'Program deleted successfully'
            ]);
        } else {
            return response()->json([
                'message' => 'Failed to delete program'
            ], 500);
        }
    }
}
