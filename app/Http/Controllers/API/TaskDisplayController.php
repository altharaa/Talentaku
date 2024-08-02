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
        $tasks = $request->getTask();
        $user = $request->user();
        $roles = $user->roles()->pluck('name')->toArray();

        if (in_array('Guru SD', $roles) || in_array('Guru KB', $roles)) {
            return TaskResource::collection($tasks);
        } else if (in_array('Murid SD', $roles) || in_array('Murid KB', $roles)) {
            $tasksWithSubmissionInfo = $tasks->map(function ($task) use ($user) {
                $hasSubmitted = $task->submissions()->where('student_id', $user->id)->exists();
                return [
                    'task_id' => $task->id,
                    'have_submit' => $hasSubmitted,
                    'task' => new TaskResource($task),
                ];
            });

            $hasSubmittedAny = $tasksWithSubmissionInfo->contains('have_submit', true);

            return response()->json([
                'message' => $hasSubmittedAny
                    ? 'You have submitted some assignments.'
                    : 'You have not submitted any assignments yet.',
                'data' => $tasksWithSubmissionInfo
            ]);
        }

        return response()->json([
            'message' => 'Unauthorized role',
        ], 403);
    }

    public function showById(TaskShowByIdRequest $request)
    {
        $task = $request->getTask();
        return new TaskResource($task);
    }

}
