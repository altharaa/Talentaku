<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskSubmissionResource;
use App\Models\TaskSubmission;
use App\Models\TaskSubmissionMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskSubmissionController extends Controller
{
    public function correction(Request $request)
    {
        $validated = $request->validate([
            'score' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $taskSubmission = TaskSubmission::findOrFail($request->submissionId);
            $taskSubmission->update($validated);

            DB::commit();

            return $this->resUpdateData($taskSubmission);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resError($e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'media' => 'nullable|array',
            'media.*' => 'file|mimes:jpg,jpeg,png,mp4,avi,mov,pdf,doc,docx,xls,xlsx,ppt,pptx|max:2048'
        ]);

        DB::beginTransaction();
        try {
            $taskSubmission = TaskSubmission::create([
                'task_id' => $request->taskId,
                'student_id' => $request->user()->id,
            ]);

            if ($request->hasFile('media')) {
                foreach ($request->file('media') as $file) {
                    $path = $file->store('task-submission-media', 'public');
                    TaskSubmissionMedia::create([
                        'submission_id' => $taskSubmission->id,
                        'original_file_name' => $file->getClientOriginalName(),
                        'file_name' => basename($path),
                    ]);
                }
            }

            DB::commit();

            return $this->resStoreData(new TaskSubmissionResource($taskSubmission));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resError($e->getMessage(), 500);
        }
    }

    public function getAll(Request $request)
    {
        $taskSubmissions =TaskSubmission::where('task_id', $request->taskId)
                            ->with(['student:id,fullname', 'task', 'task.grade'])
                            ->latest()
                            ->get();

        return TaskSubmissionResource::collection($taskSubmissions);
    }

    public function getById(Request $request)
    {
        $taskSubmission = TaskSubmission::where('id', $request->submissionId)
                            ->with(['student:id,fullname', 'task', 'task.grade'])
                            ->first();

        return new TaskSubmissionResource($taskSubmission);
    }
}
