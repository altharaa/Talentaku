<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Grade;

class GradeAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $gradeId = $request->route('gradeId'); 

        $grade = Grade::find($gradeId);

        $isTeacher = $user->id === $grade->teacher_id;

        $isStudentInGrade = $grade->members()->where('users.id', $user->id)->exists();

        if ($isTeacher || $isStudentInGrade) {
            return $next($request);
        }

        return response()->json([
            'message' => 'Access denied.',
        ], 403);
    }
}
