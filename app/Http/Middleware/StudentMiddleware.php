<?php

namespace App\Http\Middleware;

use App\Models\Grade;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StudentMiddleware
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

        if (!$grade || !$grade->members()->where('users.id', $user->id)->exists()) {
            return response()->json(['message' => 'Access denied.'], 403);
        }

        return $next($request);
    }
}
