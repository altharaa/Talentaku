<?php

namespace App\Http\Controllers\API\Task;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Task;
use App\Models\TaskLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TeacherController extends Controller
{
    private function deleteMedia($mediaToDelete, $task)
    {
        $deletedMedia = [];
        if (is_array($mediaToDelete) && !empty($mediaToDelete)) {
            foreach ($mediaToDelete as $mediaId) {
                $media = $task->media()->find($mediaId);
                if ($media) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $media->file_path));
                    $media->delete();
                    $deletedMedia[] = $mediaId;
                }
            }
        }
        return $deletedMedia;
    }

    private function uploadNewMedia($newMedia, $task)
    {
        $mediaData = [];
        if (is_array($newMedia)) {
            foreach ($newMedia as $mediaFile) {
                $fileName = Str::uuid() . '.' . $mediaFile->getClientOriginalExtension();
                $path = $mediaFile->storeAs('tasks', $fileName, 'public');
                if (!$path) {
                    throw new \Exception('Failed to upload file');
                }
                $media = $task->media()->create([
                    'file_path' => Storage::url($path)
                ]);
                $mediaData[] = [
                    'id' => $media->id,
                    'file_name' => $fileName,
                    'original_name' => $mediaFile->getClientOriginalName(),
                    'file_path' => $task->file_path,
                    'file_size' => $mediaFile->getSize(),
                    'file_type' => $mediaFile->getMimeType(),
                ];
            }
        }
        return $mediaData;
    }

    public function store(Request $request, $gradeId)
    {
        $user = $request->user();
        $grade = Grade::findOrFail($gradeId);
        $roles = $user->roles()->pluck('name')->toArray();
        if (!in_array('Guru SD', $roles) && !in_array('Guru KB', $roles)){
            return response()->json([
                'status' => 'error',
                'message' => 'Only (Guru SD or Guru KB) can create grades.',
            ], 403);
        }

        if ($user->id !== $grade->teacher_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to create task for this grade.',
            ], 403);
        }

        $data = $request->validate([
            'title' => 'required|string',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'desc' => 'required|string',
            'media' => 'nullable|array',
            'media.*' => 'file|mimes:jpg,jpeg,png,mp4,avi,mov,pdf,doc,docx,xls,xlsx,ppt,pptx|max:2048',
            'links' => 'nullable|array',
            'links.*' => 'url',
        ]);

        DB::beginTransaction();
        try{
            $task = new Task($data);
            $task->teacher_id = $user->id;
            $task->grade_id = $grade->id;
            $task->save();

            $mediaData = [];
            if ($request->hasFile('media')) {
                $mediaData = $this->uploadNewMedia($request->file('media'), $task);
            }

            $links = [];
            if ($request->has('links')) {
                foreach ($request->input('links') as $link) {
                    $taskLink = TaskLink::create([
                        'task_id' => $task->id,
                        'link' => $link,
                    ]);
                    $links[] = $taskLink;
                }
            } 

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Task created successfully',
                'data' => [
                    'grade' => $grade,
                    'task' => $task,
                    'teacher'=> $user,
                    'media' => $mediaData,
                    'links' => $links,
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

    public function update(Request $request, $gradeId, $taskId)
    {
        $user = $request->user();
        $grade = Grade::findOrFail($gradeId);
        $roles = $user->roles()->pluck('name')->toArray();

        if (!in_array('Guru SD', $roles) && !in_array('Guru KB', $roles)){
            return response()->json([
                'status' => 'error',
                'message' => 'Only (Guru SD or Guru KB) can create grades.',
            ], 403);
        }

        if ($user->id !== $grade->teacher_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to perform this action for the specified grade.',
            ], 403);
        }
    
        $task = Task::findOrFail($taskId);

        $validatedData = $request->validate([
            'title' => 'required|string',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'desc' => 'required|string',
            'media' => 'nullable|array',
            'media.*' => 'file|mimes:jpg,jpeg,png,mp4,avi,mov,pdf,doc,docx,xls,xlsx,ppt,pptx|max:2048',
            'delete_media' => 'nullable|array',
            'delete_media.*' => 'exists:task_media,id',
            'links' => 'nullable|array',
            'links.*' => 'url',
            'delete_links' => 'nullable|array',
            'delete_links.*' => 'exists:task_links,id',
        ]);
    
        DB::beginTransaction();
        try {
            $task->fill($validatedData);
            $task->save();
    
            $deletedMedia = [];
            $newMedia = [];
            if($request->has('delete_media')){
                $deletedMedia = $this->deleteMedia($request->delete_media, $task);
            }
            if($request->hasFile('media')) {
                $newMedia = $this->uploadNewMedia($request->file('media'), $task);
            }

            $deletedLinks = [];
            $newLinks = [];
            if ($request->has('delete_links')) {
                foreach ($request->input('delete_links') as $linkId) {
                    $link = TaskLink::where('id', $linkId)->where('task_id', $task->id)->first();
                    if ($link) {
                        $link->delete();
                        $deletedLinks[] = $linkId;
                    }
                }
            }
            if ($request->has('links')) {
                foreach ($request->input('links') as $link) {
                    $taskLink = TaskLink::create([
                        'task_id' => $task->id,
                        'link' => $link,
                    ]);
                    $newLinks[] = $taskLink;
                }
            }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Student report updated successfully',
                'data' => [
                    'grade' => $grade,
                    'task' => $task,
                    'teacher' => $user,
                    'new_media' => !empty($newMedia) ? $newMedia : null,
                    'deleted_media' => !empty($deletedMedia) ? $deletedMedia : null,
                    'new_link' => !empty($newLinks) ? $newLinks : null,
                    'deleted_link' => !empty($deletedLinks) ? $deletedLinks : null,
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update task: ' . $e->getMessage(),
            ], 500);
        }
    }   

    public function destroy(Request $request, $gradeId, $taskId)
    {
        $user = $request->user();
        $grade = Grade::findOrFail($gradeId);
        $roles = $user->roles()->pluck('name')->toArray();

        if (!in_array('Guru SD', $roles) && !in_array('Guru KB', $roles)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Only (Guru SD or Guru KB) can delete tasks.',
            ], 403);
        }

        if ($user->id !== $grade->teacher_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to perform this action for the specified grade.',
            ], 403);
        }

        $task = Task::where('id', $taskId)->where('grade_id', $gradeId)->first();

        if (!$task) {
            return response()->json([
                'status' => 'error',
                'message' => 'Task not found.',
            ], 404);
        }

        DB::beginTransaction();
        try {
            foreach ($task->media as $media) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $media->file_path));
                $media->delete();
            }

            $task->links()->delete();
            $task->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Task and associated data deleted successfully',
                'data' => [
                    'task_id' => $taskId,
                    'grade_id' => $gradeId
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete task: ' . $e->getMessage(),
            ], 500);
        }
    }
}
