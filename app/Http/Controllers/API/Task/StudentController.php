<?php

namespace App\Http\Controllers\API\Task;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Task;
use App\Models\TaskSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    private function uploadNewMedia($newMedia, $submission)
    {
        $mediaData = [];
        if (is_array($newMedia)) {
            foreach ($newMedia as $mediaFile) {
                $path = $mediaFile->storePublicly('task_submissions', 'public');
                if (!$path) {
                    throw new \Exception('Failed to upload file');
                }
                $media = $submission->media()->create([
                    'file_path' => Storage::url($path),
                    'submission_id' => $submission->id
                ]);
                $mediaData[] = [
                    'id' => $media->id,
                    'original_name' => $mediaFile->getClientOriginalName(),
                    'file_path' => $media->file_path,
                    'file_size' => $mediaFile->getSize(),
                    'file_type' => $mediaFile->getMimeType(),
                ];
            }
        }
        return $mediaData;
    }

    public function store(Request $request, $gradeId, $taskId)
    {
        $user = $request->user();
        $grade = Grade::findOrFail($gradeId);
        $roles = $user->roles()->pluck('name')->toArray();
        if (!in_array('Murid SD', $roles) && !in_array('Murid KB', $roles)){
            return response()->json([
                'status' => 'error',
                'message' => 'Only (Murid SD or Murid KB) can upload submission.',
            ], 403);
        }

        if (!$grade->members()->where('users.id', $user->id)->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not member for this grade.',
            ], 403);
        }

        $task = Task::findOrFail($taskId);

        $data = $request->validate([
            'media' => 'nullable|array',
            'media.*' => 'file|mimes:jpg,jpeg,png,mp4,avi,mov,pdf,doc,docx,xls,xlsx,ppt,pptx|max:2048'
        ]);

        DB::beginTransaction();
        try{
            $submission = new TaskSubmission($data);
            $submission->task_id = $task->id;
            $submission->student_id = $user->id;
            $submission->save();

            $mediaData = [];
            if ($request->hasFile('media')) {
                $mediaData = $this->uploadNewMedia($request->file('media'), $submission);
            }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Submission upload successfully',
                'data' => [
                    'user'=> $user->name,
                    'grade' => [
                        'id' =>$grade->id,
                        'name' => $grade->name,
                    ],
                    'task' => [
                        'id' => $task->id,
                        'title' => $task->title,
                    ],
                    'submission' => $submission,
                    'media' => $mediaData,
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create task: ' . $e->getMessage(),
            ], 500);
        }
    }
}
