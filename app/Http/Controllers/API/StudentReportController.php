<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\StudentReport;
use App\Models\StudentReportMedia;
use App\Models\User;
use App\Rules\ValidTeacherInGrade;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
        // 'media.*' => 'required|file|max:20480|mimes:jpeg,png,mp4,mov,avi',
    ]);

        $grade = Grade::findOrFail($gradeId);
        $student = User::findOrFail($request->student_id);

        $studentReport = new StudentReport();
        $studentReport->fill($validatedData);
        $studentReport->teacher_id = $request->user()->id;
        $studentReport->student_id = $student->id;
        $studentReport->grade_id = $grade->id;
        $studentReport->save();
    
        foreach ($request->file('media') as $media) {
            $mediaPath = $media->store('student_reports');
            $studentReportMedia = new StudentReportMedia();
            $studentReportMedia->student_report_id = $studentReport->id;
            $studentReportMedia->path = $mediaPath;
            $studentReportMedia->save();
        }
    }
    
}