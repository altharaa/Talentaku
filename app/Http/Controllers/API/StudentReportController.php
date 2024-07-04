<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\StudentReport;
use App\Models\StudentReportMedia;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StudentReportController extends Controller
{
    public function index(Request $request, $gradeId) {
        $user = $request->user();
        $grade = Grade::find($gradeId);

        if (!$grade) {
            return response()->json([
                'status' => 'error',
                'message' => 'Grade not found.',
            ], 404);
        }

        if ($user->id !== $grade->teacher_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not for the specified grade.',
            ], 403);
        }

        $studentReports = StudentReport::where('grade_id', $gradeId)->with('media')->get();

        return response()->json([
            'status' => 'success',
            'data' => $studentReports
        ]);
    }
    public function store(Request $request, $gradeId) {
        $user = $request->user();
        $grade = Grade::find($gradeId);

        if (!$grade) {
            return response()->json([
                'status' => 'error',
                'message' => 'Grade not found.',
            ], 404);
        }

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
        ]);

        $grade = Grade::findOrFail($gradeId);
        $student = User::findOrFail($request->student_id);

        DB::beginTransaction();

        try {
            $studentReport = new StudentReport();
            $studentReport->fill($validatedData);
            $studentReport->teacher_id = $request->user()->id;
            $member =  $studentReport->student_id = $student->id;
            $studentReport->grade_id = $grade->id;
            if (!$grade->members()->where('users.id', $member)->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'The specified member is not in this grade.',
                ], 404);
            }
            $studentReport->save();

            $mediaData = [];

            if($request->hasFile('media')) {
                foreach($request->file('media') as $mediaFile) {
                    $originalName = $mediaFile->getClientOriginalName();
                    $extension = $mediaFile->getClientOriginalExtension();
                    $fileName = Str::uuid() . '.' . $extension;

                    $path = $mediaFile->storeAs('student-reports', $fileName, 'public');

                    if (!$path) {
                        throw new \Exception('Failed to upload file');
                    }

                    $studentReportMedia = new StudentReportMedia();
                    $studentReportMedia->student_report_id = $studentReport->id;
                    $studentReportMedia->file_path = Storage::url($path);
                    $studentReportMedia->save();

                    $mediaData[] = [
                        'id' => $studentReportMedia->id,
                        'file_name' => $fileName,
                        'original_name' => $originalName,
                        'file_path' => $studentReportMedia->file_path,
                        'file_size' => $mediaFile->getSize(),
                        'file_type' => $mediaFile->getMimeType(),
                    ];
                }
            }
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Student report created successfully',
                'data' => [
                    'student_report' => $studentReport,
                    'media' => $mediaData
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
        
    }
    public function show(Request $request, $gradeId, $studentReportId) {
        $user = $request->user();
        $grade = Grade::find($gradeId);

        if (!$grade) {
            return response()->json([
                'status' => 'error',
                'message' => 'Grade not found.',
            ], 404);
        }

        if ($user->id !== $grade->teacher_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to perform this action for the specified grade.',
            ], 403);
        }

        $studentReport = StudentReport::with('media')->find($studentReportId);

        if (!$studentReport) {
            return response()->json([
                'status' => 'error',
                'message' => 'Student report not found.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $studentReport
        ]);
    }

    public function update(Request $request, $gradeId, $studentReportId)
    {
        $user = $request->user();
        $grade = Grade::findOrFail($gradeId);
        
        if (!$grade) {
            return response()->json([
                'status' => 'error',
                'message' => 'Grade not found.',
            ], 404);
        }
        if ($user->id !== $grade->teacher_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to perform this action for the specified grade.',
            ], 403);
        }
        $studentReport = StudentReport::find($studentReportId);
        if (!$studentReport) {
            return response()->json([
                'status' => 'error',
                'message' => 'Student report not found.',
            ], 404);
        }
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
    
        $roles = $user->roles()->pluck('name')->toArray();
        if (!in_array('Guru SD', $roles) && !in_array('Guru KB', $roles)){
            return response()->json([
                'status' => 'error',
                'message' => 'Only (Guru SD or Guru KB) can update grades.',
            ], 403);
        }
    
        $student = User::findOrFail($request->student_id);
        DB::beginTransaction();
        try {
            $studentReport->fill($validatedData);
            $studentReport->teacher_id = $request->user()->id;
            $studentReport->student_id = $student->id;
            $studentReport->grade_id = $grade->id;

            if (!$grade->members()->where('users.id', $student->id)->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'The specified member is not in this grade.',
                ], 404);
            }
    
            $studentReport->save();
    
            $deletedMedia = [];
            if ($request->has('delete_media')) {
                foreach ($request->delete_media as $mediaId) {
                    $media = StudentReportMedia::where('id', $mediaId)
                                               ->where('student_report_id', $studentReport->id)
                                               ->first();
                    if ($media) {
                        Storage::disk('public')->delete(str_replace('/storage/', '', $media->file_path));
                        
                        $media->delete();
                        $deletedMedia[] = $mediaId;
                    }
                }
            }
    
            $mediaData = [];
            if($request->hasFile('media')) {
                foreach($request->file('media') as $mediaFile) {
                    $originalName = $mediaFile->getClientOriginalName();
                    $extension = $mediaFile->getClientOriginalExtension();
                    $fileName = Str::uuid() . '.' . $extension;
                    $path = $mediaFile->storeAs('student_reports', $fileName, 'public');
                    if (!$path) {
                        throw new \Exception('Failed to upload file');
                    }
                    $studentReportMedia = new StudentReportMedia();
                    $studentReportMedia->student_report_id = $studentReport->id;
                    $studentReportMedia->file_path = Storage::url($path);
                    $studentReportMedia->save();
                    $mediaData[] = [
                        'id' => $studentReportMedia->id,
                        'file_name' => $fileName,
                        'original_name' => $originalName,
                        'file_path' => $studentReportMedia->file_path,
                        'file_size' => $mediaFile->getSize(),
                        'file_type' => $mediaFile->getMimeType(),
                    ];
                }
            }
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
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request, $gradeId, $studentReportId)
    {
        $user = $request->user();
        $grade = Grade::findOrFail($gradeId);

        if (!$grade) {
            return response()->json([
                'status' => 'error',
                'message' => 'Grade not found.',
            ], 404);
        }

        if ($user->id !== $grade->teacher_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to perform this action for the specified grade.',
            ], 403);
        }

        $studentReport = StudentReport::where('id', $studentReportId)->where('grade_id', $gradeId)->first();

        if (!$studentReport) {
            return response()->json([
                'status' => 'error',
                'message' => 'Student report not found in the specified grade.',
            ], 404);
        }

        $roles = $user->roles()->pluck('name')->toArray();
        if (!in_array('Guru SD', $roles) && !in_array('Guru KB', $roles)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Only (Guru SD or Guru KB) can delete student reports.',
            ], 403);
        }

        DB::beginTransaction();

        try {
            $media = StudentReportMedia::where('student_report_id', $studentReport->id)->get();
            foreach ($media as $item) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $item->file_path));
                $item->delete();
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