<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProgramController extends Controller
{
    public function show() 
    {
        $programs = Program::all();

        return response()->json([
            'programs' => $programs
        ]);   
    }

    public function store(Request $request)
    {
        $validatedData = $request->all();

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo')->storePublicly('photos', 'public');
            $validatedData['photo'] = Storage::url($photo);
        }

        $program = Program::create($validatedData);

        return response()->json([
            'message' => 'Program created successfully',
            'program' => $program
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $program = Program::findOrFail($id);
        $validatedData = $request->all();

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo')->storePublicly('photos', 'public');
            $validatedData['photo'] = Storage::url($photo);
        }

        $program->update($validatedData);

        return response()->json([
            'message' => 'Program updated successfully',
            'program' => $program
        ]);
    }

    public function destroy($id)
    {
        $program = Program::findOrFail($id);
        $program->delete();

        return response()->json([
            'message' => 'Program deleted successfully'
        ]);
    }
}
