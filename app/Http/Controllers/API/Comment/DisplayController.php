<?php

namespace App\Http\Controllers\API\Comment;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Grade;
use Illuminate\Http\Request;

class DisplayController extends Controller
{
    public function detail(Request $request, $gradeId, $commentId)
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
            $comment = Comment::with(['user', 'reply.user', 'media'])->findOrFail($commentId);
    
            $formattedComment = [
                'id' => $comment->id,
                'user' => [
                    'id' => $comment->user->id,
                    'name' => $comment->user->name,
                    'photo' => $comment->user->photo
                ],
                'comments' => explode(PHP_EOL, $comment->comments),
                'media' => [],
                'created_at' => $comment->created_at,
                'updated_at' => $comment->updated_at,
                'replies' => [],
            ];
    
            if ($comment->reply) {
                $formattedComment['replies'] = $comment->reply->map(function ($reply) {
                    return [
                        'id' => $reply->id,
                        'user' => [
                            'id' => $reply->user->id,
                            'name' => $reply->user->name,
                            'photo' => $reply->user->photo
                        ],
                        'replies' => explode(PHP_EOL, $reply->replies),
                        'created_at' => $reply->created_at,
                        'updated_at' => $reply->updated_at
                    ];
                })->toArray();
            }
    
            if ($comment->media) {
                $formattedComment['media'] = $comment->media->map(function ($media) {
                    return [
                        'id' => $media->id,
                        'file_path' => $media->file_path,
                    ];
                })->toArray();
            }
    
            return response()->json([
                'status' => 'success',
                'comment' => $formattedComment
            ], 200);
    
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Comment not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve comment details',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
