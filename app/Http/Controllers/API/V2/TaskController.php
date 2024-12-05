<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\TaskLink;
use App\Models\TaskMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Laravel\Firebase\Facades\Firebase;

use function PHPUnit\Framework\returnSelf;

class TaskController extends Controller
{
    protected $notification;

    public function __construct()
    {
        $this->notification = Firebase::messaging();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'end_date' => 'required|date|after_or_equal:today',
            'desc' => 'required|string',
            'media' => 'nullable|array',
            'media.*' => 'nullable|file|mimes:jpg,jpeg,png,mp4,avi,mov,pdf,doc,docx,xls,xlsx,ppt,pptx|max:2048',
            'links' => 'nullable|array',
            'links.*' => 'nullable|url',
        ]);

        DB::beginTransaction();
        try {
            $task = Task::create([
                'title' => $validated['title'],
                'start_date' => now()->toDateString(),
                'end_date' => $validated['end_date'],
                'desc' => $validated['desc'],
                'grade_id' => $request->gradeId,
                'teacher_id' => $request->user()->id,
            ]);

            if ($request->hasFile('media')) {
                foreach ($request->file('media') as $file) {
                    $path = $file->store('task-media', 'public');
                    TaskMedia::create([
                        'task_id' => $task->id,
                        'original_file_name' => $file->getClientOriginalName(),
                        'file_name' => basename($path),
                    ]);
                }
            }

            if ($request->has('links')) {
                foreach ($request->input('links') as $link) {
                    TaskLink::create([
                        'task_id' => $task->id,
                        'link' => $link,
                    ]);
                }
            }


            $students = $task->grade->members;
            foreach ($students as $student) {
                if ($student->fcm_token != null) {
                    $message = CloudMessage::withTarget('token', $student->fcm_token)
                        ->withNotification([
                            'title' => 'Tugas Siswa',
                            'body' => 'Guru Membuat Tugas Baru',
                        ]);
                    $this->notification->send($message);
                }
            }

            DB::commit();
            return new TaskResource($task);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resError($e, 500);
        }
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'end_date' => 'required|date|after_or_equals:today',
            'desc' => 'required|string',
            'media' => 'nullable|array',
            'media.*' => 'nullable|file|mimes:jpg,jpeg,png,mp4,avi,mov,pdf,doc,docx,xls,xlsx,ppt,pptx|max:2048',
            'delete_media' => 'nullable|array',
            'delete_media.*' => 'nullable|integer|exists:task_media,id',
            'links' => 'nullable|array',
            'links.*' => 'nullable|url',
            'delete_links' => 'nullable|array',
            'delete_links.*' => 'nullable|integer|exists:task_links,id',
        ]);

        DB::beginTransaction();
        try {
            $task = Task::findOrFail($request->taskId);
            $task->update([
                'title' => $validated['title'],
                'start_date' => now()->toDateString(),
                'end_date' => $validated['end_date'],
                'desc' => $validated['desc'],
            ]);

            if ($request->hasFile('media')) {
                foreach ($request->file('media') as $file) {
                    $path = $file->store('task-media', 'public');
                    TaskMedia::create([
                        'task_id' => $task->id,
                        'original_file_name' => $file->getClientOriginalName(),
                        'file_name' => basename($path),
                    ]);
                }
            }

            if ($request->has('links')) {
                TaskLink::where('task_id', $task->id)->delete();
                foreach ($request->input('links') as $link) {
                    TaskLink::create([
                        'task_id' => $task->id,
                        'link' => $link,
                    ]);
                }
            }

            if (!empty($validated['delete_media'])) {
                foreach ($validated['delete_media'] as $mediaId) {
                    $media = TaskMedia::find($mediaId);
                    if ($media) {
                        $filePath = '/storage/task-media/' . $media->file_name;
                        if (Storage::disk('public')->exists($filePath)) {
                            Storage::disk('public')->delete($filePath);
                        }
                        $media->delete();
                    }
                }
            }

            if (!empty($validated['delete_links'])) {
                foreach ($validated['delete_links'] as $linkId) {
                    $taskLink = TaskLink::find($linkId);
                    if ($taskLink) {
                        $taskLink->delete();
                    }
                }
            }            

            DB::commit();
            return new TaskResource($task);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resError($e, 500);
        }
    }

    public function delete(Request $request)
    {
        $task = Task::findOrFail($request->route('taskId'));

        DB::beginTransaction();
        try {
            foreach ($task->media as $media) {
                $filePath = 'task-media/' . $media->file_name;
                if (Storage::disk('public')->exists($filePath)) {
                    $media->delete();
                }
            }

            $task->links()->delete();
            $task->delete();

            DB::commit();
            return $this->resDeleteData('Task Deleted Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resError($e, 500);
        }
    }

    public function getAll(Request $request)
    {
        $tasks = Task::where('grade_id', $request->route('gradeId'))->get();
        return TaskResource::collection($tasks);
    }

    public function getDetail(Request $request)
    {
        $task = Task::findOrFail($request->route('taskId'));
        return new TaskResource($task);
    }
}
