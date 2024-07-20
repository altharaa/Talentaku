<?php

namespace App\Http\Controllers\API\task;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Task;
use Illuminate\Http\Request;

class DisplayController extends Controller
{
    public function show(Request $request, $gradeId)
    {
        $user = $request->user();
        $grade = Grade::findOrFail($gradeId);

        if ($user->id != $grade->teacher_id && !$grade->members->contains($user->id)) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to view tasks for this grade.',
            ], 403);
        }

        try {
            $tasks = Task::where('grade_id', $gradeId)
                ->with(['media', 'links'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Tasks retrieved successfully',
                'data' => [
                    'grade' => $grade->only(['id', 'name']),
                    'tasks' => $tasks->map(function ($task) {
                        return [
                            'id' => $task->id,
                            'title' => $task->title,
                            'start_date' => $task->start_date,
                            'end_date' => $task->end_date,
                            'desc' => $task->desc,
                            'media' => $task->media->map(function ($media) {
                                return [
                                    'id' => $media->id,
                                    'file_path' => $media->file_path,
                                    'file_name' => $media->file_name,
                                ];
                            }),
                            'links' => $task->links->map(function ($link) {
                                return [
                                    'id' => $link->id,
                                    'link' => $link->link,
                                ];
                            }),
                            'created_at' => $task->created_at,
                            'updated_at' => $task->updated_at,
                        ];
                    }),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve tasks: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function detail(Request $request, $gradeId, $taskId)
    {
        $user = $request->user();
        $grade = Grade::findOrFail($gradeId);

        if ($user->id != $grade->teacher_id && !$grade->members->contains($user->id)) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to view this task.',
            ], 403);
        }

        try {
            $task = Task::where('id', $taskId)
                ->where('grade_id', $gradeId)
                ->with(['media', 'links'])
                ->first();

            if (!$task) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Task not found.',
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Task details retrieved successfully',
                'data' => [
                    'grade' => [
                        'id' => $grade->id,
                        'name' => $grade->name,
                    ],
                    'task' => [
                        'id' => $task->id,
                        'title' => $task->title,
                        'start_date' => $task->start_date,
                        'end_date' => $task->end_date,
                        'desc' => $task->desc,
                        'media' => $task->media->map(function ($media) {
                            return [
                                'id' => $media->id,
                                'file_path' => $media->file_path,
                            ];
                        }),
                        'links' => $task->links->map(function ($link) {
                            return [
                                'id' => $link->id,
                                'link' => $link->link,
                            ];
                        }),
                        'created_at' => $task->created_at->toDateTimeString(),
                        'updated_at' => $task->updated_at->toDateTimeString(),
                    ],
                    'teacher' => [
                        'id' => $grade->teacher->id,
                        'name' => $grade->teacher->name,
                    ],
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve task details: ' . $e->getMessage(),
            ], 500);
        }
    }
}
