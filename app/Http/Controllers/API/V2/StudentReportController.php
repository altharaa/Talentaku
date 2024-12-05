<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentReportResource;
use App\Models\StudentReport;
use App\Models\StudentReportMedia;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Laravel\Firebase\Facades\Firebase;

class StudentReportController extends Controller
{
    protected $notification;

    public function __construct()
    {
        $this->notification = Firebase::messaging();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'created' => 'required|date',
            'semester' => 'required|',
            'kegiatan_awal_dihalaman' => 'required|string',
            'dihalaman_hasil' => 'required|',
            'kegiatan_awal_berdoa' => 'required|string',
            'berdoa_hasil' => 'required|',
            'kegiatan_inti_satu' => 'required|string',
            'inti_satu_hasil' => 'required|',
            'kegiatan_inti_dua' => 'nullable|string',
            'inti_dua_hasil' => 'nullable|',
            'kegiatan_inti_tiga' => 'nullable|string',
            'inti_tiga_hasil' => 'nullable|',
            'snack' => 'required|string',
            'inklusi' => 'required|string',
            'inklusi_hasil' => 'required|',
            'inklusi_penutup' => 'required|',
            'inklusi_penutup_hasil' => 'required|',
            'inklusi_doa' => 'required|string',
            'inklusi_doa_hasil' => 'required|',
            'catatan' => 'required|string',
            'media' => 'nullable|array',
            'media.*' => 'file|mimes:jpg,jpeg,png,pdf,mp4|max:10240',
            'student_id' => 'required|integer|exists:users,id',
        ]);

        DB::beginTransaction();
        try {        
            $studentReport = new StudentReport($validated);
            $studentReport->teacher_id = $request->user()->id;
            $studentReport->save();

            if ($request->hasFile('media')) {
                foreach ($request->file('media') as $file) {
                    $path = $file->store('student-report-media', 'public');
                    StudentReportMedia::create([
                        'student_report_id' => $studentReport->id,
                        'file_path' => basename($path),
                    ]);
                }
            }

            $student = User::where('id', $validated['student_id'])->first();
            if ($student->fcm_token != null) {
                $message = CloudMessage::withTarget('token', $student->fcm_token)
                    ->withNotification([
                        'title' => 'Laporan Harian Siswa',
                        'body' => 'Guru Membuat Laporan Baru',
                    ]);
                $this->notification->send($message);
            }

            DB::commit();

            return $this->resStoreData(new StudentReportResource($studentReport));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resError($e->getMessage(), 500);
        }
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'created' => 'required|date',
            'semester' => 'required|',
            'kegiatan_awal_dihalaman' => 'required|string',
            'dihalaman_hasil' => 'required|',
            'kegiatan_awal_berdoa' => 'required|string',
            'berdoa_hasil' => 'required|',
            'kegiatan_inti_satu' => 'required|string',
            'inti_satu_hasil' => 'required|',
            'kegiatan_inti_dua' => 'nullable|string',
            'inti_dua_hasil' => 'nullable|',
            'kegiatan_inti_tiga' => 'nullable|string',
            'inti_tiga_hasil' => 'nullable|',
            'snack' => 'required|string',
            'inklusi' => 'required|string',
            'inklusi_hasil' => 'required|',
            'inklusi_penutup' => 'required|',
            'inklusi_penutup_hasil' => 'required|',
            'inklusi_doa' => 'required|string',
            'inklusi_doa_hasil' => 'required|',
            'catatan' => 'required|string',
            'media' => 'nullable|array',
            'media.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,mp4|max:10240',
            'delete_media' => 'nullable|array',
            'delete_media.*' => 'nullable|integer|exists:student_report_media,id',
        ]);

        DB::beginTransaction();
        try {
            $studentReport = StudentReport::findOrFail($request->studentReportId);
            unset($validated['teacher_id']);
            $studentReport->update($validated);
            $studentReport->save();

            if ($request->hasFile('media')) {
                foreach ($request->file('media') as $file) {
                    $path = $file->store('student-report-media', 'public');
                    StudentReportMedia::create([
                        'student_report_id' => $studentReport->id,
                        'file_path' => basename($path),
                    ]);
                }
            }

            if (!empty($validated['delete_media'])) {
                foreach ($validated['delete_media'] as $mediaId) {
                    $media = StudentReportMedia::find($mediaId);
                    if ($media) {
                        $filePath = '/storage/student-report-media/' . $media->file_name;
                        if (Storage::disk('public')->exists($filePath)) {
                            Storage::disk('public')->delete($filePath);
                        }
                        $media->delete();
                    }
                }
            }
            
            DB::commit();

            return new StudentReportResource($studentReport);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resError($e->getMessage(), 500);
        }
    }

    public function delete(Request $request)
    {
        $studentReport = StudentReport::findOrFail($request->studentReportId);

        DB::beginTransaction();
        try {
            foreach ($studentReport->media as $media) {
                $filePath = '/storage/student-report-media/' . $media->file_path;
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
                $media->delete();
            }

            $studentReport->delete();

            DB::commit();

            return $this->resDeleteData('Student report deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resError($e->getMessage(), 500);
        }
    }

    public function getAllByTeacher(Request $request)
    {
        $studentReports = StudentReport::where('student_id', $request->studentId)->get();
        return StudentReportResource::collection($studentReports);
    }

    public function getDetailByTeacher(Request $request)
    {
        $studentReport = StudentReport::findOrFail($request->studentReportId);
        return new StudentReportResource($studentReport);
    }

    public function getAllByStudent(Request $request)
    {
        $studentReports = StudentReport::where('student_id', $request->user()->id)->get();
        return StudentReportResource::collection($studentReports);
    }

    public function getDetailByStudent(Request $request)
    {
        $studentReport = StudentReport::findOrFail($request->studentReportId);
        return new StudentReportResource($studentReport);
    }
}
