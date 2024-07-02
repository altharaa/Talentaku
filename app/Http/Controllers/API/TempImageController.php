<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\TempStudentReportMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TempImageController extends Controller
{
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'path_name' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validatedData = $validator->validated();

        if ($request->hasFile('path_name')) {
            $photo = $request->file('path_name')->storePublicly('photos', 'public');
            $validatedData['path_name'] = Storage::url($photo);
        }

        $tempStudentReportMedia = new TempStudentReportMedia();
        $tempStudentReportMedia->fill($validatedData);
        $tempStudentReportMedia->save();

        return response()->json([
            'path_name' => $tempStudentReportMedia->path_name,
        ]);
    }
}
