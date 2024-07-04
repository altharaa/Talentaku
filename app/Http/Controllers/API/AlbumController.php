<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\AlbumMedia;
use App\Models\Grade;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AlbumController extends Controller
{
    public function index(Request $request, $gradeId)
    {
        $user = $request->user();
        $grade = Grade::findOrFail($gradeId);

        if (!$grade) {
            return response()->json([
                'status' => 'error',
                'message' => 'Grade not found.',
            ], 404);
        }

        if ($user->id !== $grade->teacher_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized for the specified grade.',
            ], 403);
        }

        $album = Album::where('grade_id', $gradeId)->with('media')->get();

        return response()->json([
            'status' => 'success',
            'data' => $album
        ]);
    }
    public function show() 
    {
        
    }
    public function store(Request $request, $gradeId)
    {
        $user = $request->user();

        $grade = Grade::find($gradeId);
        if (!$grade) {
            return response()->json([
                'status' => 'error',
                'message' => 'Grade not found.',
            ], 404);
        }

        $roles = $user->roles()->pluck('name')->toArray();
        if (!in_array('Guru SD', $roles) && !in_array('Guru KB', $roles)){
            return response()->json([
                'status' => 'error',
                'message' => 'Only (Guru SD or Guru KB) can create grades.',
            ], 403);
        }

        if ($user->id !== $grade->teacher_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to perform this action for the specified grade.',
            ], 403);
        }

        $validatedData = $request->validate([
            'desc' => 'required|string',
            'media' => 'required|array',
            'media.*' => 'file|mimes:jpeg,png,jpg,gif,svg,mp4,mov,avi|max:20480',
        ]);

        $grade = Grade::findOrFail($gradeId);

        DB::beginTransaction();

        try {
            $album = new Album();
            $album->fill($validatedData);
            $album->grade_id = $gradeId;
            $album->teacher_id = $user->id;
            $album->date = today();
            $album->save();

            $albumId = $album->id; 

            $mediaData = [];
            if($request->hasFile('media')) {
                foreach ($request->file('media') as $file) {
                    $originalName = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
                    $fileName = Str::uuid() . '.' . $extension;

                    $path = $file->storeAs('album-media', $fileName, 'public');

                    if (!$path) {
                        throw new \Exception('Failed to upload file');
                    }

                    $albumMedia = new AlbumMedia();
                    $albumMedia->album_id = $albumId; 
                    $albumMedia->file_path = Storage::url($path);
                    $albumMedia->save();

                    $mediaData[] = [
                        'id' => $albumMedia->id,
                        'file_name' => $fileName,
                        'original_name' => $originalName,
                        'file_path' => $albumMedia->file_path,
                        'file_size' => $file->getSize(),
                        'file_type' => $file->getMimeType(),
                    ];
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Album successfully created.',
                'data' => [
                    'student_report' => $album,
                    'media' => $mediaData
                ]
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    } 
}
