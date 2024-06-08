<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\AlbumPhoto;
use App\Models\Grade;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
    public function store(Grade $grade, Request $request)
    {
        $request->validate([
            'title' => 'required',
            'desc' => 'required',
            'date' => 'required|date',
            'photo' => 'required|array|',
            'photos.*' => 'reaquired|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $album = Album::create([
            'title' => $request->title,
            'desc' => $request->desc,
            'date' => $request->date,
            'grade_id' => $grade->id,
        ]);

        foreach ($request->photos as $photo) {
            $photoPath = $photo->store('photos', 'public');

            AlbumPhoto::create([
                'album_id' => $album->id,
                'photo' => $photoPath,
            ]);
        }

        return response()->json([
            'message' => 'Album and photos created successfully',
            'album' => $album,
        ], 201);
    } 
}
