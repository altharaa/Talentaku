<?php

namespace App\Http\Controllers\API\Comment;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\CommentReply;
use App\Models\Grade;
use App\Models\Reply;
use Illuminate\Http\Request;

class ReplyController extends Controller
{
    public function store(Request $request, $gradeId, $commentId)
    {
        $user = $request->user();
        $grade = Grade::findOrFail($gradeId);

        if (($user->id !== $grade->teacher_id) && (!$grade->members->contains($user->id))) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to create comments for this grade.',
            ], 403);
        }

        $request->validate([
            'replies' => 'required|string',
        ]);

        try {
            $comment = Comment::findOrFail($commentId);
            $reply = new CommentReply();
            $reply->user_id = $user->id;
            $reply->comment_id = $comment->id;
            $reply->replies = $request->replies;
            $reply->save();

            $reply->load('user');

            $formattedReply = [
                'id' => $reply->id,
                'user' => [
                    'id' => $reply->user_id,
                    'name' => optional($reply->user)->name,
                    'photo' => optional($reply->user)->photo
                ],
                'replies' => explode(PHP_EOL, $reply->replies),
                'created_at' => $reply->created_at,
                'updated_at' => $reply->updated_at,
            ];

            return response()->json([
                'message' => 'Reply created successfully',
                'reply' => $formattedReply,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create reply',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $gradeId, $commentId, $replyId)
    {
        $user = $request->user();
        $grade = Grade::findOrFail($gradeId);

        if (($user->id !== $grade->teacher_id) && (!$grade->members->contains($user->id))) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to create comments for this grade.',
            ], 403);
        }

        $request->validate([
            'replies' => 'required|string',
        ]);

        try {
            $reply = CommentReply::findOrFail($replyId);

            if ($reply->user_id !== $user->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not authorized to delete this reply.',
                ], 403);
            }

            $reply->replies = $request->replies;
            $reply->save();

            $reply->load('user');
            $formattedReply = [
                'id' => $reply->id,
                'user' => [
                    'id' => $reply->user_id,
                    'name' => optional($reply->user)->name,
                    'photo' => optional($reply->user)->photo
                ],
                'replies' => explode(PHP_EOL, $reply->replies),
                'created_at' => $reply->created_at,
                'updated_at' => $reply->updated_at,
            ];

            return response()->json([
                'message' => 'Reply updated successfully',
                'reply' => $formattedReply,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update reply',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request, $gradeId, $commentId, $replyId)
    {
        $user = $request->user();
        $grade = Grade::findOrFail($gradeId);

        if (($user->id !== $grade->teacher_id) && (!$grade->members->contains($user->id))) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to access comments for this grade.',
            ], 403);
        }

        try {
            $comment = Comment::findOrFail($commentId);
            $reply = CommentReply::find($replyId);

            if ($user->id !== $comment->user_id && $user->id !== $reply->user_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not authorized to update this reply.',
                ], 403);
            }

            $reply->delete();

            return response()->json([
                'message' => 'Comment-Reply deleted successfully',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Comment or reply not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete reply',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
