<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskSubmissionCorrectionRequest;
use App\Http\Resources\TaskSubmissionResource;
use App\Models\Grade;
use App\Models\Task;
use App\Models\TaskSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class TaskSubmissionCorrectionController extends Controller
{
    public function correction(TaskSubmissionCorrectionRequest $request)
    {
        $submission = $request->getTaskSubmission();

        try {
            $submission->score = $request->score;
            $submission->save();

            return $this->resStoreData(new TaskSubmissionResource($submission));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resError($e->getMessage(), 500);
        }
    }
}
