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

}
