<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementDisplayController extends Controller
{
    public function index($gradeId)
    {
        try {
            $announcements = Announcement::where('grade_id', $gradeId)
                ->with('user:id,name')
                ->get();

            $formattedAnnouncements = $announcements->map(function ($announcement) {
                return [
                    'id' => $announcement->id,
                    'title' => $announcement->title,
                    'content' => $announcement->content,
                    'created_at' => $announcement->created_at,
                    'updated_at' => $announcement->updated_at,
                    'user' => [
                        'id' => $announcement->user->id,
                        'name' => $announcement->user->name,
                    ],
                    'media' => $announcement->media,
                    'replies_count' => $announcement->reply->count(),
                ];
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Announcements retrieved successfully',
                'data' => $formattedAnnouncements,
            ], 200);
        } catch (\Exception $e) {
            return $this->resError($e->getMessage(), 500);
        }
    }
}
