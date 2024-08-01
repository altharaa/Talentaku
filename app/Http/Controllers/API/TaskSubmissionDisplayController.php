<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskSubmissionDisplayRequest;
use App\Http\Resources\TaskSubmissionResource;

class TaskSubmissionDisplayController extends Controller
{
    public function completions(TaskSubmissionDisplayRequest $request)
    {
        try {
            $completions = $request->getCompletionsScoreNull();
            return new TaskSubmissionResource($completions);
        } catch (\Exception $e) {
            return $this->resError($e->getMessage(),500);
        }
    }

    public function show(TaskSubmissionDisplayRequest $request)
    {
        try {
            $completions = $request->getCompletionsById();
            return new TaskSubmissionResource($completions);
        } catch (\Exception $e) {
            return $this->resError($e->getMessage(),500);
        }
    }

    public function completionsWithScores(TaskSubmissionDisplayRequest $request)
    {
        try {
            $completions = $request->getCompletionsWithScores();
            return new TaskSubmissionResource($completions);
        } catch (\Exception $e) {
            return $this->resError($e->getMessage(),500);
        }
    }
}
