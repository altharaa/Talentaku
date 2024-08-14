<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\GradeContentRequest;
use App\Http\Resources\GradeContentResource;
use App\Models\Announcement;
use App\Models\Grade;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GradeContentController extends Controller
{
    public function index(GradeContentRequest $request, $gradeId): AnonymousResourceCollection
    {

        $tasks = $request->getTasks();
        $comments = Announcement::where('grade_id', $gradeId)
            ->with('user:id,name,photo')
            ->get();

        $combinedContent = $tasks->concat($comments)->sortByDesc('created_at');

        return GradeContentResource::collection($combinedContent)
            ->additional([
                'status' => 'success',
                'message' => 'Content retrieved successfully',
                'grade' => $request->getGrade(),
            ]);
    }
}
