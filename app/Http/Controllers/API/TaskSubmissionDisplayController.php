<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskSubmissionDisplayRequest;
use App\Http\Resources\TaskSubmissionResource;
use App\Models\Grade;
use App\Models\Task;
use App\Models\TaskSubmission;
use Carbon\Carbon;

class TaskSubmissionDisplayController extends Controller
{

    public function completions(TaskSubmissionDisplayRequest $request)
    {
        try {
            $completions = $request->getCompletions();
            return new TaskSubmissionResource($completions);
        } catch (\Exception $e) {
            return $this->resError($e->getMessage(),500);
        }
    }
}
