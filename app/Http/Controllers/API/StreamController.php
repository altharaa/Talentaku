<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Grade;
use App\Models\Task;
use Illuminate\Http\Request;

class StreamController extends Controller
{
    public function index(Request $request, $gradeId)
    {
        $user = $request->user();
        $grade = Grade::findOrFail($gradeId);

        if ($user->id != $grade->teacher_id && !$grade->members->contains($user->id)) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to view content for this grade.',
            ], 403);
        }

        try {
            $tasks = Task::where('grade_id', $gradeId)->get();
            $comments = Comment::where('grade_id', $gradeId)
                ->with('user:id,name,photo')
                ->get();

            $combinedContent = $tasks->concat($comments)->sortByDesc('created_at');

            $formattedContent = $combinedContent->map(function ($item) {
                $contentType = $item instanceof Task ? 'task' : 'comment';
                $baseData = [
                    'id' => $item->id,
                    'type' => $contentType,
                    'created_at' => $item->created_at,
                ];

                if ($contentType === 'task') {
                    $baseData['preview'] = $item->title;
                } else {
                    $baseData['preview'] = substr(explode(PHP_EOL, $item->comments)[0], 0, 75) . '...';
                    $baseData['user'] = [
                        'id' => $item->user->id,
                        'name' => $item->user->name,
                        'photo' => $item->user->photo,
                    ];
                }

                return $baseData;
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Content retrieved successfully',
                'data' => [
                    'grade' => $grade->only(['id', 'name']),
                    'content' => $formattedContent,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve content: ' . $e->getMessage(),
            ], 500);
        }
    }

//    public function show(Request $request, $gradeId, $contentId)
//    {
//        $user = $request->user();
//        $grade = Grade::findOrFail($gradeId);
//
//        if ($user->id != $grade->teacher_id && !$grade->members->contains($user->id)) {
//            return response()->json([
//                'status' => 'error',
//                'message' => 'You are not authorized to view this content.',
//            ], 403);
//        }
//
//        try {
//            $tasks = Task::where('grade_id', $gradeId)->get();
//            $comments = Comment::where('grade_id', $gradeId)
//                ->with('user:id,name,photo')
//                ->get();
//
//            $combinedContent = $tasks->concat($comments)->sortByDesc('created_at');
//
//            $formattedContent = $combinedContent->map(function ($item, $index) {
//                $contentType = $item instanceof Task ? 'task' : 'comment';
//                return [
//                    'id' => $item->id,
//                    'type' => $contentType,
//                    'created_at' => $item->created_at,
//                    'preview' => $contentType === 'task' ? $item->title : substr(explode(PHP_EOL, $item->comments)[0], 0, 75) . '...',
//                    'user' => $contentType === 'comment' ? [
//                        'id' => $item->user->id,
//                        'name' => $item->user->name,
//                        'photo' => $item->user->photo,
//                    ] : null,
//                ];
//            })->reverse()->values();
//
//            $item = $formattedContent->get($contentId);
//
//            if (!$item) {
//                return response()->json([
//                    'status' => 'error',
//                    'message' => 'Content not found in this grade.',
//                ], 404);
//            }
//
//            if ($item['type'] === 'task') {
//                $task = Task::where('id', $item['id'])
//                    ->where('grade_id', $gradeId)
//                    ->with(['media', 'links'])
//                    ->first();
//
//                return $this->formatTaskResponse($task);
//            } else {
//                $comment = Comment::where('id', $item['id'])
//                    ->where('grade_id', $gradeId)
//                    ->with(['user', 'reply.user', 'media'])
//                    ->first();
//
//                return $this->formatCommentResponse($comment);
//            }
//
//        } catch (\Exception $e) {
//            return response()->json([
//                'status' => 'error',
//                'message' => 'Failed to retrieve content details: ' . $e->getMessage(),
//            ], 500);
//        }
//    }
//
//    private function formatTaskResponse($task)
//    {
//        return response()->json([
//            'status' => 'success',
//            'message' => 'Task details retrieved successfully',
//            'data' => [
//                'type' => 'task',
//                'id' => $task->id,
//                'title' => $task->title,
//                'start_date' => $task->start_date,
//                'end_date' => $task->end_date,
//                'desc' => $task->desc,
//                'media' => $task->media->map(function ($media) {
//                    return [
//                        'id' => $media->id,
//                        'file_path' => $media->file_path,
//                    ];
//                }),
//                'links' => $task->links->map(function ($link) {
//                    return [
//                        'id' => $link->id,
//                        'link' => $link->link,
//                    ];
//                }),
//                'created_at' => $task->created_at,
//                'updated_at' => $task->updated_at,
//            ]
//        ], 200);
//    }
//
//    private function formatCommentResponse($comment)
//    {
//        return response()->json([
//            'status' => 'success',
//            'message' => 'Comment details retrieved successfully',
//            'data' => [
//                'type' => 'comment',
//                'id' => $comment->id,
//                'user' => [
//                    'id' => $comment->user->id,
//                    'name' => $comment->user->name,
//                    'photo' => $comment->user->photo
//                ],
//                'comments' => explode(PHP_EOL, $comment->comments),
//                'media' => $comment->media->map(function ($media) {
//                    return [
//                        'id' => $media->id,
//                        'file_path' => $media->file_path,
//                    ];
//                }),
//                'created_at' => $comment->created_at,
//                'updated_at' => $comment->updated_at,
//                'replies' => $comment->reply->map(function ($reply) {
//                    return [
//                        'id' => $reply->id,
//                        'user' => [
//                            'id' => $reply->user->id,
//                            'name' => $reply->user->name,
//                            'photo' => $reply->user->photo
//                        ],
//                        'replies' => explode(PHP_EOL, $reply->replies),
//                        'created_at' => $reply->created_at,
//                        'updated_at' => $reply->updated_at
//                    ];
//                }),
//            ]
//        ], 200);
//    }
}
