<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\User;
use Illuminate\Http\Request;

class GradeController extends Controller
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
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::find($request->user_id);

        if ($user && ($user->role == 'Guru SD' || $user->role == 'Guru KB')) {
            Grade::create([
                'name' => $request->name,
                'user_id' => $request->user_id,
            ]);

            return response()->json(['message' => 'Grade created successfully'], 201);
        } else {
            return response()->json(['message' => 'User must be a Teacher'], 403);
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
