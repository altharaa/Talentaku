<?php

namespace App\Http\Middleware;

use App\Models\Grade;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TeacherMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user(); 
        $gradeId = $request->route('gradeId'); 

        $grade = Grade::find($gradeId);

        if (!$grade) {
            return response()->json([
                'message' => 'Grade not found.',
            ], 404);
        }

        if ($user->id !== $grade->teacher_id) {
            return response()->json([
                'message' => 'You are not authorized to access this class.',
            ], 403);
        }


        return $next($request);
    }
}
