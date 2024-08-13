<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskDestroyRequest;
use App\Http\Requests\TaskStoreRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Http\Resources\TaskResource;
use App\Models\Grade;
use App\Models\Task;
use App\Models\TaskLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
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
                $path = $mediaFile->store('public/tasks');
                if (!$path) {
                    throw new \Exception('Failed to upload file : ' . $mediaFile->getClientOriginalName());
                }
                $media = $task->media()->create([
                    'file_name' => $path,
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

    public function store(TaskStoreRequest $request, $gradeId)
    {
        DB::beginTransaction();
        try {
            $task = new Task();
            $task->fill([
                'title' => $request->input('title'),
                'desc' => $request->input('desc'),
                'start_date' => now()->toDateString(),
                'end_date' => $request->input('end_date'),
                'grade_id' => $gradeId,
                'teacher_id' => $request->user()->id,
            ]);
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

             $task = Task::with(['grade', 'teacher', 'media', 'links'])->find($task->id);

            return $this->resStoreData($task);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resError($e, 500);
        }
    }

    public function update(TaskUpdateRequest $request, $taskId)
    {
        $task = $request->getTask();

        DB::beginTransaction();
        try {
            $task->fill($request->validated());
            $task->save();

            $deletedMedia = [];
            $newMedia = [];
            if ($request->has('delete_media')) {
                $deletedMedia = $this->deleteMedia($request->delete_media, $task);
            }
            if ($request->hasFile('media')) {
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

            $task->load(['grade', 'teacher', 'media', 'links']);

            return $this->resUpdateData(new TaskResource($task));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resError($e,500);
        }
    }
    public function destroy(TaskDestroyRequest $request)
    {
        $task = $request->getTask();

        DB::beginTransaction();
        try {
            foreach ($task->media as $media) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $media->file_path));
                $media->delete();
            }

            $task->links()->delete();
            $task->delete();

            DB::commit();

            return $this->resDeleteData('Task');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resError($e, 500);
        }
    }
}
