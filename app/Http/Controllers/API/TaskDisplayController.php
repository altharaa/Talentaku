<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskShowByGradeRequest;
use App\Http\Requests\TaskShowByIdRequest;
use App\Http\Resources\TaskResource;
use App\Models\Grade;
use App\Models\Task;
use App\Models\TaskSubmission;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TaskDisplayController extends Controller
{
    public function showByGrade(TaskShowByGradeRequest $request)
    {
        $grade = $request->getGrade();
    
        $task = $grade->tasks()->with('media', 'links')->latest()->get();
    
        return response()->json([
            'status' => 'success',
            'message' => $task->isEmpty() ? 'No tasks found for this grade.' : 'Tasks retrieved successfully.',
            'data' => TaskResource::collection($task),
        ]);
    }

    public function showById(TaskShowByIdRequest $request)
    {
        $task = $request->getTask();
        return new TaskResource($task);
    }

    public function completions(Request $request, $gradeId, $taskId)
    {
        $user = $request->user();
        $grade = Grade::findOrFail($gradeId);

        if ($user->id != $grade->teacher_id && !$grade->members->contains($user->id)) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to view task completions for this grade.',
            ], 403);
        }

        try {
            $task = Task::where('id', $taskId)
                ->where('grade_id', $gradeId)
                ->firstOrFail();

            $completions = TaskSubmission::where('task_id', $taskId)
                ->with('student:id,name') 
                ->get()
                ->map(function ($submission) use ($task) {
                    $submissionDate = Carbon::parse($submission->created_at);
                    $isLate = $submission->created_at > $task->end_date;
                    return [
                        'student_name' => optional($submission->student)->name,
                        'submitted_at' =>  $submissionDate->toDateString(),
                        'is_late' => $isLate,
                        'status' => $isLate ? 'Late' : 'On Time',
                    ];
                });

            return response()->json([
                'status' => 'success',
                'message' => 'Task completions retrieved successfully',
                'data' => [
                    'grade' => [
                        'id' => $grade->id,
                        'name' => $grade->name,
                    ],
                    'task' => [
                        'id' => $task->id,
                        'title' => $task->title,
                        'end_date' => Carbon::parse($task->end_date)->toDateString(),
                    ],
                    'completions' => $completions,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve task completions: ' . $e->getMessage(),
            ], 500);
        }
    }

}
