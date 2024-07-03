<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\StudentReport;
use App\Models\StudentReportMedia;
use App\Models\TempStudentReportMedia;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StudentReportController extends Controller
{
    public function store(Request $request, $gradeId) {
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
            $studentReport->student_id = $student->id;
            $studentReport->grade_id = $grade->id;
            $studentReport->save();

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
    
}