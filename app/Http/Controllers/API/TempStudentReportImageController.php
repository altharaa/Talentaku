<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\TempStudentReportMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TempStudentReportImageController extends Controller
{
    public function store(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'media' => 'required|file|mimes:jpeg,png,jpg,gif,svg,mp4,mov,avi|max:20480',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        if ($request->hasFile('media')) {
            $file = $request->file('media');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $fileName = Str::uuid() . '.' . $extension;

            $path = $file->storeAs('temp/student_reports', $fileName, 'public');

            if (!$path) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to upload file',
                ], 500);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'File uploaded successfully',
                'data' => [
                    'file_name' => $fileName,
                    'original_name' => $originalName,
                    'file_path' => Storage::url($path),
                    'file_size' => $file->getSize(),
                    'file_type' => $file->getMimeType(),
                ]
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'No file was uploaded',
        ], 400);
    }
}
