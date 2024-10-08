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
use App\Models\Grade;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Laravel\Firebase\Facades\Firebase;

class AlbumController extends Controller
{
    protected $notification;
    public function __construct()
    {
        $this->notification = Firebase::messaging();
    }
    public function showByGrade(AlbumShowByGradeRequest $request)
    {
        $grade = $request->getGrade();

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
                if (!$path) {
                    throw new Exception('Failed to upload file: ' . $originalName);
                }
                $albumMedia = new AlbumMedia([
                    'album_id' => $album->id,
                    'file_name' => basename($path),
                ]);
                $albumMedia->save();
                $mediaData[] = $albumMedia;
            }

            $album->media = $mediaData;
            DB::commit();

            $grade = Grade::findOrFail($gradeId);
            $users = User::whereIn('id', $grade->members->pluck('id'))->get();

            foreach ($users as $user) {
                if ($user->fcm_token != null) {
                    $message = CloudMessage::withTarget('token', $user->fcm_token)
                        ->withNotification([
                            'title' => 'Album Kegiatan Siswa',
                            'body' => 'Guru Menambahkan Album Kegiatan Siswa',
                        ]);
                    $this->notification->send($message);
                }
            }
            return $this->resStoreData(new AlbumResource($album));
        } catch (Exception $e) {
            DB::rollBack();
            return $this->resError($e->getMessage(), 500);
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
