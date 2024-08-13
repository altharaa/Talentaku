<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AnnouncementRequest;
use App\Http\Requests\AnnouncementStoreRequest;
use App\Http\Requests\AnnouncementUpdateRequest;
use App\Http\Resources\AnnouncementResource;
use App\Models\Announcement;
use App\Models\AnnouncementMedia;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    public function store(AnnouncementStoreRequest $request, $gradeId)
    {
        $user = $request->user();

        DB::beginTransaction();
        try {
            $announcement = new Announcement();
            $announcement->user_id = $user->id;
            $announcement->grade_id = $gradeId;
            $announcement->announcements = $request->announcements;
            $announcement->save();

            if ($request->hasFile('media')) {
                foreach ($request->file('media') as $file) {
                    $path = $file->store('announcement_media', 'public');
                    AnnouncementMedia::create([
                        'announce_id' => $announcement->id,
                        'file_name' => $path,
                    ]);
                }
            }
            DB::commit();
            return $this->resStoreData(new AnnouncementResource($announcement));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resError($e->getMessage(), 500);
        }
    }

    public function update(AnnouncementUpdateRequest $request)
    {
        $announcement = $request->getAnnouncement();

        DB::beginTransaction();
        try {
            $announcement->announcements = $request->announcements;
            $announcement->save();

            if ($request->has('delete_media')) {
                foreach ($request->delete_media as $mediaId) {
                    $media = AnnouncementMedia::find($mediaId);
                    if ($media) {
                        Storage::disk('public')->delete($media->file_name);
                        $media->delete();
                    }
                }
            }

            if ($request->hasFile('media')) {
                foreach ($request->file('media') as $file) {
                    $path = $file->store('announcement_media', 'public');
                    AnnouncementMedia::create([
                        'announce_id' => $announcement->id,
                        'file_name' => $path,
                    ]);
                }
            }
            DB::commit();

            return $this->resUpdateData(new AnnouncementResource($announcement));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resError($e->getMessage(), 500);
        }
    }

    public function destroy( Request $request,$gradeId, $commentId)
    {
        $user = $request->user();
        $grade = Grade::findOrFail($gradeId);

        if (($user->id != $grade->teacher_id) && (!$grade->members->contains($user->id))) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to create comments for this grade.',
            ], 403);
        }

        try{
            $comment = Announcement::find($commentId);
            $comment->delete();

            if ($comment->user_id != $user->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not authorized to delete this reply.',
                ], 403);
            }

            return response()->json([
                'message' => 'Comment deleted successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create comment',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
