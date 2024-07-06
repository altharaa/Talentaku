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
        $roles = $user->roles->pluck('name')->toArray(); 
        $isStudent = in_array('Murid SD', $roles) || in_array('Murid KB', $roles);
        $isTeacher = in_array('Guru SD', $roles) || in_array('Guru KB', $roles);

        if (!$isStudent && !$isTeacher) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access. Only students and teachers can view albums.',
            ], 403);
        }

        $grade = Grade::findOrFail($gradeId);

        if ($isStudent && !$grade->members->contains($user->id)) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not a member of this grade.',
            ], 403);
        }
    
        if ($isTeacher && $grade->teacher_id !== $user->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not the teacher of this grade.',
            ], 403);
        }

        $albums = $grade->albums()->with('media')->get();

        return response()->json([
            'status' => 'success',
            'message' => $albums->isEmpty() ? 'No albums found for this grade.' : 'Albums retrieved successfully.',
            'data' => $albums,
        ]);
    }

    public function show(Request $request, $gradeId, $albumId) 
    {
        $user = $request->user();
        $grade = Grade::findOrFail($gradeId);

        if ($user->id !== $grade->teacher_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to view albums for this grade.',
            ], 403);
        }

        $album = Album::with('media')->where('grade_id', $gradeId)->findOrFail($albumId);

        return response()->json([
            'status' => 'success',
            'data' => $album
        ]);

    }

    public function store(Request $request, $gradeId)
    {
        $user = $request->user();
        $grade = Grade::findOrFail($gradeId);

        $roles = $user->roles()->pluck('name')->toArray();
        if (!in_array('Guru SD', $roles) && !in_array('Guru KB', $roles)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Only teachers (Guru SD or Guru KB) can create albums.',
            ], 403);
        }

        if ($user->id !== $grade->teacher_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to create an album for this grade.',
            ], 403);
        }

        $validatedData = $request->validate([
            'desc' => 'required|string',
            'media' => 'required|array',
            'media.*' => 'file|mimes:jpeg,png,jpg,gif,svg,mp4,mov,avi|max:20480',
        ]);

        DB::beginTransaction();

        try {
            $album = new Album();
            $album->fill([
                'desc' => $validatedData['desc'],
                'grade_id' => $gradeId,
                'teacher_id' => $user->id,
                'date' => now()->toDateString(),
            ]);
            $album->save();

            $mediaData = [];
            foreach ($request->file('media') as $file) {
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileName = Str::uuid() . '.' . $extension;
                $path = $file->storeAs('album-media', $fileName, 'public');
                
                if (!$path) {
                    throw new Exception('Failed to upload file: ' . $originalName);
                }
    
                $albumMedia = new AlbumMedia([
                    'album_id' => $album->id,
                    'file_path' => Storage::url($path),
                ]);
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

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Album successfully created.',
                'data' => [
                    'album' => $album,
                    'media' => $mediaData
                ]
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create album: ' . $e->getMessage(),
            ], 500);
        }
    } 

    public function destroy(Request $request, $gradeId, $albumId) 
    {
        $user = $request->user();
        $grade = Grade::findOrFail($gradeId);

        if ($user->id !== $grade->teacher_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to perform this action for the specified grade.',
            ], 403);
        }
    
        $roles = $user->roles()->pluck('name')->toArray();
        if (!in_array('Guru SD', $roles) && !in_array('Guru KB', $roles)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Only teachers (Guru SD or Guru KB) can delete albums.',
            ], 403);
        }

        $album = Album::where('id', $albumId)->where('grade_id', $gradeId)->firstOrFail();

        DB::beginTransaction();
        try {
            $media = $album->media;
            foreach ($media as $item) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $item->file_path));
            }
            $album->delete(); 
            DB::commit();
    
            return response()->json([
                'status' => 'success',
                'message' => 'Album and associated media deleted successfully',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete album: ' . $e->getMessage(),
            ], 500);
        }
    }
}
