<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskSubmissionRequest;
use App\Http\Resources\TaskSubmissionResource;
use App\Models\Task;
use App\Models\TaskSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskSubmissionController extends Controller
{
    private function uploadNewMedia($newMedia, $submission)
    {
        $mediaData = [];
        if (is_array($newMedia)) {
            foreach ($newMedia as $mediaFile) {
                $path = $mediaFile->store('public/task-submissions');
                if (!$path) {
                    throw new \Exception('Failed to upload file');
                }
                $media = $submission->media()->create([
                    'file_name' => basename($path),
                ]);
                $mediaData[] = [
                    'id' => $media->id,
                    'file_name' => $media->file_name,
                    'file_size' => $mediaFile->getSize(),
                    'file_type' => $mediaFile->getMimeType(),
                ];
            }
        }
        return $mediaData;
    }

    public function store(TaskSubmissionRequest $request)
    {
        $user = $request->user();
        $task = $request->getTask();

        DB::beginTransaction();
        try {
            $submission = new TaskSubmission($request->validated());
            $submission->task_id = $task->id;
            $submission->student_id = $user->id;
            $submission->save();

            if ($request->hasFile('media')) {
                $this->uploadNewMedia($request->file('media'), $submission);
            }

            $submission->load(['student', 'task.grade', 'media']);

            DB::commit();

            return $this->resStoreData(new TaskSubmissionResource($submission));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resError($e->getMessage(), 500);
        }
    }
}
