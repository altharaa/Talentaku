<?php

namespace App\Http\Controllers\API\StudentReport;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\StudentReport;
use App\Models\StudentReportMedia;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TeacherController extends Controller
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
                $fileName = Str::uuid() . '.' . $mediaFile->getClientOriginalExtension();
                $path = $mediaFile->storeAs('student_reports', $fileName, 'public');
                if (!$path) {
                    throw new \Exception('Failed to upload file');
                }
                $studentReportMedia = $studentReport->media()->create([
                    'file_path' => Storage::url($path)
                ]);
                $mediaData[] = [
                    'id' => $studentReportMedia->id,
                    'file_name' => $fileName,
                    'original_name' => $mediaFile->getClientOriginalName(),
                    'file_path' => $studentReportMedia->file_path,
                    'file_size' => $mediaFile->getSize(),
                    'file_type' => $mediaFile->getMimeType(),
                ];
            }
        }
        return $mediaData;
    }

    public function store(Request $request, $gradeId) {
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
                'message' => 'You are not authorized to create reports for this grade.',
            ], 403);
        }

       $validatedData = $request->validate([
            'created' => 'required|date',
            'semester_id' => 'required|exists:student_report_semesters,id',
            'kegiatan_awal' => 'required|string',
            'awal_point' => 'required|in:Muncul,Kurang,Belum Muncul',
            'kegiatan_inti' => 'required|string',
            'inti_point' => 'required|in:Muncul,Kurang,Belum Muncul',
            'snack' => 'required|string',
            'snack_point' => 'required|in:Muncul,Kurang,Belum Muncul',
            'inklusi' => 'required|string',
            'inklusi_point' => 'required|in:Muncul,Kurang,Belum Muncul',
            'catatan' => 'required|string',
            'student_id' => 'required|exists:users,id',
            'media' => 'nullable|array',
            'media.*' => 'file|mimes:jpeg,png,jpg,gif,svg,mp4,mov,avi|max:20480',
        ]);

        $student = User::findOrFail($request->student_id);

        if (!$grade->members()->where('users.id', $student->id)->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'The specified student is not in this grade.',
            ], 404);
        }

        DB::beginTransaction();

        try {
            $studentReport = new StudentReport($validatedData);
            $studentReport->teacher_id = $user->id;
            $studentReport->student_id = $student->id;
            $studentReport->grade_id = $grade->id;
            $studentReport->save();

            $mediaData = $this->uploadNewMedia($request->file('media'), $studentReport);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Student report created successfully',
                'data' => [
                    'semester_name' => optional($studentReport->semester)->name,
                    'student_report' => $studentReport,
                    'media' => $mediaData
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create student report: ' . $e->getMessage(),
            ], 500);
        }
        
    }   

    public function update(Request $request, $gradeId, $studentReportId)
    {
        $user = $request->user();
        $grade = Grade::findOrFail($gradeId);

        if ($user->id !== $grade->teacher_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to perform this action for the specified grade.',
            ], 403);
        }
    
        $roles = $user->roles()->pluck('name')->toArray();
        if (!in_array('Guru SD', $roles) && !in_array('Guru KB', $roles)){
            return response()->json([
                'status' => 'error',
                'message' => 'Only (Guru SD or Guru KB) can create grades.',
            ], 403);
        }
    
        $studentReport = StudentReport::findOrFail($studentReportId);

        $validatedData = $request->validate([
            'created' => 'required|date',
            'level' => 'required|in:Semester 1,Semester 2',
            'kegiatan_awal' => 'required|string',
            'awal_point' => 'required|in:Muncul,Kurang,Belum Muncul',
            'kegiatan_inti' => 'required|string',
            'inti_point' => 'required|in:Muncul,Kurang,Belum Muncul',
            'snack' => 'required|string',
            'snack_point' => 'required|in:Muncul,Kurang,Belum Muncul',
            'inklusi' => 'required|string',
            'inklusi_point' => 'required|in:Muncul,Kurang,Belum Muncul',
            'catatan' => 'required|string',
            'student_id' => 'required|exists:users,id',
            'media' => 'nullable|array',
            'media.*' => 'file|mimes:jpeg,png,jpg,gif,svg,mp4,mov,avi|max:20480',
            'delete_media' => 'nullable|array',
            'delete_media.*' => 'exists:student_report_media,id'
        ]);
    
        $student = User::findOrFail($request->student_id);

        if (!$grade->members()->where('users.id', $student->id)->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'The specified student is not in this grade.',
            ], 404);
        }
    
        DB::beginTransaction();

        try {
            $studentReport->fill($validatedData);
            $studentReport->teacher_id = $user->id;
            $studentReport->student_id = $student->id;
            $studentReport->grade_id = $grade->id;
            $studentReport->save();
    
            $deletedMedia = [];
    
            $deletedMedia = $this->deleteMedia($request->delete_media, $studentReport);
            $mediaData = $this->uploadNewMedia($request->file('media'), $studentReport);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Student report updated successfully',
                'data' => [
                    'student_report' => $studentReport,
                    'new_media' => $mediaData,
                    'deleted_media' => $deletedMedia,
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update student report: ' . $e->getMessage(),
            ], 500);
        }
    }   

    public function destroy(Request $request, $gradeId, $studentReportId)
    {
        $user = $request->user();
        $grade = Grade::findOrFail($gradeId);

        if ($user->id !== $grade->teacher_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to perform this action for the specified grade.',
            ], 403);
        }
    
        $roles = $user->roles()->pluck('name')->toArray();
        if (!in_array('Guru SD', $roles) && !in_array('Guru KB', $roles)){
            return response()->json([
                'status' => 'error',
                'message' => 'Only (Guru SD or Guru KB) can create grades.',
            ], 403);
        }
    
        $studentReport = StudentReport::where('id', $studentReportId)
                                      ->where('grade_id', $gradeId)
                                      ->firstOrFail();

        DB::beginTransaction();

        try {
            foreach ($studentReport->media as $item) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $item->file_path));
            }

            $studentReport->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Student report and associated media deleted successfully',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete student report: ' . $e->getMessage(),
            ], 500);
        }
    }
}