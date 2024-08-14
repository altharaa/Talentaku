<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\GradeContentResource;
use App\Models\Announcement;
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
            $comments = Announcement::where('grade_id', $gradeId)
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
                    new GradeContentResource($comments);
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
}
