<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentReportRequest;
use App\Http\Requests\StudentReportStoreRequest;
use App\Http\Requests\StudentReportUpdateRequest;
use App\Http\Resources\StudentReportResource;
use App\Models\StudentReport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StudentReportController extends Controller
{
    private function deleteMedia($mediaToDelete, $studentReport)
    {
        $deletedMedia = [];
        if (is_array($mediaToDelete) && !empty($mediaToDelete)) {
            foreach ($mediaToDelete as $mediaId) {
                $media = $studentReport->media()->find($mediaId);
                if ($media) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $media->file_path));
                    $media->delete();
                    $deletedMedia[] = $mediaId;
                }
            }
        }
        return $deletedMedia;
    }

    private function uploadNewMedia($newMedia, $studentReport)
    {
        $mediaData = [];
        if (is_array($newMedia)) {
            foreach ($newMedia as $mediaFile) {
                $path = $mediaFile->store('public/student-reports');
                if (!$path) {
                    throw new \Exception('Failed to upload file');
                }
                $studentReportMedia = $studentReport->media()->create([
                    'file_path' => $path,
                ]);
                $mediaData[] = [
                    'id' => $studentReportMedia->id,
                    'file_path' => $studentReportMedia->file_path,
                    'file_size' => $mediaFile->getSize(),
                    'file_type' => $mediaFile->getMimeType(),
                ];
            }
        }
        return $mediaData;
    }

    public function store(StudentReportStoreRequest $request)
    {
        $validatedData = $request->validated();
        DB::beginTransaction();

        try {
            $studentReport = new StudentReport($validatedData);
            $studentReport->teacher_id = $request->user()->id;
            $studentReport->grade_id = $request->route('gradeId');
            $studentReport->save();

            $this->uploadNewMedia($request->file('media'), $studentReport);

            DB::commit();
            return $this->resStoreData(new StudentReportResource($studentReport));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resError($e->getMessage(),500);
        }
    }

    public function update(StudentReportUpdateRequest $request)
    {
        $validatedData = $request->validated();
        $studentReport = StudentReport::findOrFail($request->route('studentReportId'));
        DB::beginTransaction();

        try {
            $studentReport->fill($validatedData);
            $studentReport->teacher_id =  $request->user()->id;
            $studentReport->grade_id = $request->route('gradeId');
            $studentReport->save();

            $this->deleteMedia($request->delete_media, $studentReport);
            $this->uploadNewMedia($request->file('media'), $studentReport);

            DB::commit();
            return $this->resUpdateData(new StudentReportResource($studentReport));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resError($e->getMessage(),500);
        }
    }

    public function destroy(StudentReportRequest $request)
    {
        $studentReport = $request->getReport();

        DB::beginTransaction();
        try {
            foreach ($studentReport->media as $item) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $item->file_path));
            }
            $studentReport->delete();
            DB::commit();

            return $this->resDeleteData('Student report and associated media deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resError($e->getMessage(), 500);
        }
    }
}
