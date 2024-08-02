<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProgramStoreRequest;
use App\Http\Requests\ProgramUpdateRequest;
use App\Http\Resources\ProgramResource;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProgramController extends Controller
{
    public function index()
    {
        $programs = Program::all();
        return ProgramResource::collection($programs);
    }

    public function showByCategory($categoryId)
    {
        $programs = Program::where('category_id', $categoryId)->get();
        return ProgramResource::collection($programs);
    }

    public function store(ProgramStoreRequest $request)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo')->store('public/photos');
            $validatedData['photo'] = basename($photo);
        }

        $program = Program::create($validatedData);
        return new ProgramResource($program);
    }

    public function update(ProgramUpdateRequest $request, $id)
    {
        $program = Program::findOrFail($id);
        $validatedData = $request->validated();

        if ($request->hasFile('photo')) {
            if ($program->photo) {
                Storage::delete('public/photos/' . $program->photo);
            }
            $photo = $request->file('photo')->store('public/photos');
            $validatedData['photo'] = basename($photo);
        }

        $program->update($validatedData);
        return new ProgramResource($program);
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $roles = $user->roles()->pluck('name')->toArray();
        if (!in_array('Guru SD', $roles) && !in_array('Guru KB', $roles)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $program = Program::findOrFail($id);
        if ($program->photo) {
            Storage::delete('public/photos/' . $program->photo);
        }
        $program->delete();

        return response()->json(['message' => 'Program deleted successfully']);
    }
}
