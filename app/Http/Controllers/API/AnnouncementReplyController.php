<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AnnouncementReplyRequest;
use App\Http\Requests\AnnouncementReplyStoreRequest;
use App\Http\Requests\AnnouncementReplyUpdateRequest;
use App\Http\Resources\AnnouoncementReplyResource;
use App\Models\AnnouncementReply;
use Illuminate\Support\Facades\DB;

class AnnouncementReplyController extends Controller
{
    public function store(AnnouncementReplyStoreRequest $request)
    {
        try {
            $announcement = $request->getAnnouncement();
            $reply = new AnnouncementReply();
            $reply->user_id = $request->user()->id;
            $reply->announce_id = $announcement->id;
            $reply->replies = $request->replies;
            $reply->save();

            return $this->resStoreData(new AnnouoncementReplyResource($reply));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resError($e->getMessage(), 500);
        }
    }

    public function update(AnnouncementReplyUpdateRequest $request)
    {
        $reply = $request->getAnnouncementReply();
        try {
            $reply->replies = $request->replies;
            $reply->save();

            return $this->resUpdateData(new AnnouoncementReplyResource($reply));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resError($e->getMessage(), 500);
        }
    }

    public function destroy(AnnouncementReplyRequest $request) {
        $reply = $request->getAnnouncementReply();
        try {
            $reply->delete();

            return response()->json([
                'message' => 'Reply deleted successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resError($e->getMessage(), 500);
        }
    }
}
