<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\AlbumPhoto;
use App\Models\Grade;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
    public function index()
    {
        $albums = Album::with(['photos', 'grade.teacher', 'grade.members'])->get();

        return response()->json($albums);
    }
    public function show() 
    {
        
    }
    public function store(Request $request, $id)
    {
        $user = $request->user();
        $roles = $user->roles->pluck('name')->toArray(); 

        if (!$user->in_array('Guru SD', $roles) || in_array('Guru KB', $roles)) {
          return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validatedData = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'date' => 'required|date',
            'photos' => 'required|array',
            'photos.*' => 'required|file|image|video',
        ]);

        $grade = $user->getGrade()->find($id);

        $album = $grade->albums()->create([
            'title' => $validatedData['title'],
            'desc' => $validatedData['description'],
            'date' => $validatedData['date'],
        ]);

        if (isset($validatedData['photos'])) {
            foreach ($validatedData['photos'] as $photo) {
                $photoPath = $photo->store('public/album_photos');
                $album->albumPhotos()->create([
                    'photo' => $photoPath,
                ]);
            }
        }

        return response()->json([
            'message' => 'Album created successfully',
            'data' => [
                'teacher' => $user->name,
                'grade' => $grade->name,
                'album' => [
                    'title' => $album->title,
                    'description' => $album->desc,
                    'date' => $album->date,
                    'photos' => $album->albumPhotos->pluck('photo'),
                ],
            ],
        ], 201);
    } 
}
