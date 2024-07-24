<?php

namespace App\Http\Controllers\API\Task;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Task;
use App\Models\TaskLink;
use App\Models\TaskSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TeacherController extends Controller
{
    public function correction(Request $request, $gradeId, $taskId, $submissionId)
    {
        $validator = Validator::make($request->all(), [
            'score' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $grade = Grade::findOrFail($gradeId);
        if ($request->user()->id != $grade->teacher_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to grade submissions for this class.',
            ], 403);
        }

        $task = Task::where('id', $taskId)->where('grade_id', $gradeId)->first();
        if (!$task) {
            return response()->json([
                'status' => 'error',
                'message' => 'Task not found in the specified grade.',
            ], 404);
        }

        $submission = TaskSubmission::where('id', $submissionId)
            ->where('task_id', $taskId)
            ->first();

        if (!$submission) {
            return response()->json([
                'status' => 'error',
                'message' => 'Submission not found.',
            ], 404);
        }

        try {
            $submission->score = $request->score;
            $submission->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Submission graded successfully',
                'data' => [
                    'submission_id' => $submission->id,
                    'task_id' => $submission->task_id,
                    'student_id' => $submission->student_id,
                    'score' => $submission->score,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to grade submission: ' . $e->getMessage(),
            ], 500);
        }
    }
}
