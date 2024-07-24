<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AlbumDestroyRequest;
use App\Http\Requests\AlbumShowByGradeRequest;
use App\Http\Requests\AlbumShowByIdRequest;
use App\Http\Requests\AlbumStoreRequest;
use App\Http\Resources\AlbumResource;
use App\Models\Album;
use App\Models\AlbumMedia;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AlbumController extends Controller
{
    public function showByGrade(AlbumShowByGradeRequest $request)
    {
        $grade = $request->getGrade();
    
        if (!$grade) {
            return response()->json([
                'status' => 'error',
                'message' => 'Grade not found.',
            ], 404);
        }
    
        $albums = $grade->albums()->with('media')->get();
    
        return response()->json([
            'status' => 'success',
            'message' => $albums->isEmpty() ? 'No albums found for this grade.' : 'Albums retrieved successfully.',
            'data' => AlbumResource::collection($albums),
        ]);
    }

    public function showById(AlbumShowByIdRequest $request)
    {
        $album = $request->getAlbum();
        return new AlbumResource($album);
    }

    public function store(AlbumStoreRequest $request, $gradeId)
    {
        DB::beginTransaction();
        try {
            $album = new Album();
            $album->fill([
                'desc' => $request->input('desc'),
                'grade_id' => $gradeId,
                'teacher_id' => $request->user()->id,
                'date' => now()->toDateString(),
            ]);
            $album->save();

            $mediaData = [];
            foreach ($request->file('media') as $file) {
                $originalName = $file->getClientOriginalName();
                $path = $file->store('public/album-media');
                $fileName = basename($path);
                if (!$path) {
                    throw new Exception('Failed to upload file: ' . $originalName);
                }
                $albumMedia = new AlbumMedia([
                    'album_id' => $album->id,
                    'file_name' => $fileName,
                ]);
                $albumMedia->save();
                $mediaData[] = $albumMedia;
            }

            $album->media = $mediaData;
            DB::commit();

            return $this->resStoreData(new AlbumResource($album));
        } catch (Exception $e) {
            DB::rollBack();
            return $this->resError($e, 500);
        }
    }

    public function destroy(AlbumDestroyRequest $request)
    {
        $album = $request->getAlbum();

        DB::beginTransaction();
        try {
            $media = $album->media;
            foreach ($media as $item) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $item->file_path));
            }
            $album->delete();
            DB::commit();

            return $this->resDeleteData('Album');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->resError($e, 500);
        }
    }
}
