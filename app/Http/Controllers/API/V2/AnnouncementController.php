<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnnouncementResource;
use App\Models\Announcement;
use App\Models\AnnouncementMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Laravel\Firebase\Facades\Firebase;

class AnnouncementController extends Controller
{
    protected $notification;

    public function __construct()
    {
        $this->notification = Firebase::messaging();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'announcements' => 'required|string',
            'media' => 'nullable|array',
            'media.*' => 'nullable|file|mimes:jpeg,png,jpg,svg,mp4,mov,pdf,docx,ppt|max:10480',
        ]);

        DB::beginTransaction();
        try {
            $announcement = Announcement::create([
                'user_id' => $request->user()->id,
                'grade_id' => $request->route('gradeId'),
                'announcements' => $validated['announcements'],
            ]);

            if ($request->hasFile('media')) {
                foreach ($request->file('media') as $file) {
                    $path = $file->store('announcement-media', 'public');
                    AnnouncementMedia::create([
                        'announce_id' => $announcement->id,
                        'original_file_name' => $file->getClientOriginalName(),
                        'file_name' => basename($path),
                    ]);
                }
            }

            DB::commit();

            $students = $announcement->grade->members;
            foreach ($students as $student) {
                if ($student->fcm_token != null) {
                    $message = CloudMessage::withTarget('token', $student->fcm_token)
                        ->withNotification([
                            'title' => 'Pemberitahuan Siswa',
                            'body' => 'Guru Menambahkan Pemberitahuan Kelas',
                        ]);
                    $this->notification->send($message);
                }
            }

            return $this->resStoreData(new AnnouncementResource($announcement));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resError( $e->getMessage(), 500);
        }

    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'announcements' => 'required|string',
            'media' => 'nullable|array',
            'media.*' => 'nullable|file|mimes:jpeg,png,jpg,svg,mp4,mov,pdf,docx,ppt|max:10480',
            'delete_media' => 'nullable|array', 
            'delete_media.*' => 'nullable|integer|exists:announcement_media,id', 
        ]);

        DB::beginTransaction();
        try {
            $announcement = Announcement::findOrFail($request->route('announcementId'));
            
            $announcement->update([
                'announcements' => $validated['announcements'],
            ]);

            if ($request->hasFile('media')) {
                foreach ($request->file('media') as $file) {
                    $path = $file->store('announcement-media', 'public');
                    AnnouncementMedia::create([
                        'announce_id' => $announcement->id,
                        'original_file_name' => $file->getClientOriginalName(),
                        'file_name' => basename($path),
                    ]);
                }
            }

            if (!empty($validated['delete_media'])) {
                foreach ($validated['delete_media'] as $mediaId) {
                    $media = AnnouncementMedia::find($mediaId);

                    if ($media) {
                        $filePath = '/storage/announcement-media/' . $media->file_name;
                        if (Storage::disk('public')->exists($filePath)) {
                            Storage::disk('public')->delete($filePath);
                        }

                        $media->delete();
                    }
                }
            }
            DB::commit();

            return $this->resUpdateData(new AnnouncementResource($announcement));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resError($e->getMessage(), 500);
        }
    }

    public function delete(Request $request)
    {
        $announcement = Announcement::findOrFail($request->route('announcementId'));

        DB::beginTransaction();
        try {
            $media = $announcement->media;

            foreach ($media as $item) {
                $filePath = 'announcement-media/' . $item->file_name; 
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
            }
    
            AnnouncementMedia::where('announce_id', $announcement->id)->delete();
            $announcement->delete();

            DB::commit(); 
            return $this->resDeleteData('Announcement Deleted Successfully');
        } catch (\Exception $e) {
            return $this->resError($e->getMessage(), 500);
        }
    }

    public function getAll(Request $request)
    {
        $annoucements = Announcement::where('grade_id', $request->route('gradeId'))->get();
        return AnnouncementResource::collection($annoucements);
    }
}
