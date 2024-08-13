<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AnnouncementReplyRequest;
use App\Http\Resources\AnnouncementResource;

class AnnouncementReplyDisplayController extends Controller
{
    public function detail(AnnouncementReplyRequest $request)
    {
        $announcementReply = $request->getAnnouncement();
        $announcementReply->load(['reply' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }]);
        return response()->json(new AnnouncementResource($announcementReply));
    }
}
