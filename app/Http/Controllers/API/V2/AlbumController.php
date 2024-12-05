<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\AlbumResource;
use App\Models\Album;
use App\Models\AlbumMedia;
use Exception;
use Illuminate\Http\Request;
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

    public function getAll(Request $request)
    {
        $albums = Album::where('grade_id', $request->route('gradeId'))->get();
        return AlbumResource::collection($albums);
    }

    public function getById(Request $request)
    {
        $albums = Album::where('id', $request->route('albumId'))
                    ->where('grade_id', $request->route('gradeId'))
                    ->get();
        return AlbumResource::collection($albums);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'desc' => 'required|string|max:255', 
            'media' => 'required|array', 
            'media.*' => 'file|mimes:jpg,jpeg,png,pdf,mp4|max:10240',
        ]);

        DB::beginTransaction();
        try {
            $album = Album::create([
                'desc' => $validated['desc'],
                'grade_id' => $request->route('gradeId'),
                'teacher_id' => $request->user()->id, 
                'date' => now()->toDateString(), 
            ]);

            $mediaData = [];
            foreach ($request->file('media') as $file) {
                $originalName = $file->getClientOriginalName();
                $path = $file->store('public/album-media');
                if (!$path) {
                    throw new Exception('Failed to upload file: ' . $originalName);
                }
                $albumMedia = AlbumMedia::create([
                    'album_id' => $album->id,
                    'file_name' => basename($path),
                ]);
                $mediaData[] = $albumMedia;
            }

            $album->media = $mediaData;
            DB::commit();

            $students = $album->grade->members;
            foreach ($students as $student) {
                if ($student->fcm_token != null) {
                    $message = CloudMessage::withTarget('token', $student->fcm_token)
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

    public function delete(Request $request)
    {
        $album = Album::where('id', $request->route('albumId'))
                    ->where('grade_id', $request->route('gradeId'))
                    ->first();

        DB::beginTransaction();
        try {
            $media = $album->media;
            foreach ($media as $item) {
                Storage::disk('public')->delete('album-media/' . $item->file_name);
            }
            $album->delete();

            DB::commit();

            return $this->resDeleteData('Album Deleted Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resError($e->getMessage(), 500);
        }
    }
}