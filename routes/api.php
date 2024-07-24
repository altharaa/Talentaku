<?php

use App\Http\Controllers\API\AlbumController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\Comment\CommentController;
use App\Http\Controllers\API\Comment\DisplayController;
use App\Http\Controllers\API\Comment\ReplyController;
use App\Http\Controllers\API\Grade\StudentController as GradeStudentController;
use App\Http\Controllers\API\Grade\TeacherController as GradeTeacherController;
use App\Http\Controllers\API\InformationController;
use App\Http\Controllers\API\ProgramController;
use App\Http\Controllers\API\StreamController;
use App\Http\Controllers\API\StudentReport\StudentController;
use App\Http\Controllers\API\StudentReport\StudentReportController;
use App\Http\Controllers\API\StudentReport\TeacherController;
use App\Http\Controllers\API\task\DisplayController as TaskDisplayController;
use App\Http\Controllers\API\Task\TeacherController as TaskTeacherController;
use App\Http\Controllers\API\Task\StudentController as TaskStudentController;
use App\Http\Controllers\API\TaskController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::prefix('user')->group(function () {
    Route::get('/', [UserController::class, 'show'])->middleware('auth:sanctum');
    Route::post('/update-photo', [UserController::class, 'updatePhoto'])->middleware('auth:sanctum');
    Route::post('/update-password', [UserController::class, 'updatePassword'])->middleware('auth:sanctum');
});

Route::prefix('programs')->group(function () {
    Route::get('/', [ProgramController::class, 'show']);
    Route::post('/add', [ProgramController::class, 'store'])->middleware('auth:sanctum');
    Route::post('/{id}', [ProgramController::class, 'update'])->middleware('auth:sanctum');
    Route::delete('/{id}', [ProgramController::class, 'destroy'])->middleware('auth:sanctum');
});

Route::prefix('information')->group(function () {
    Route::get('/', [InformationController::class, 'show']);
    Route::post('/add', [InformationController::class, 'store'])->middleware('auth:sanctum');
    Route::put('/{id}', [InformationController::class, 'update'])->middleware('auth:sanctum');
    Route::delete('/{id}', [InformationController::class, 'destroy'])->middleware('auth:sanctum');
    Route::get('/list', [InformationController::class, 'get']);
});

Route::prefix('grades')->group(function () {
    Route::post('/join', [GradeStudentController::class, 'join'])->middleware('auth:sanctum');
    Route::post('/', [GradeTeacherController::class, 'store'])->middleware('auth:sanctum');
    Route::get('/teacher', [GradeTeacherController::class, 'index'])->middleware('auth:sanctum');
    Route::get('/student', [GradeStudentController::class, 'index'])->middleware('auth:sanctum');
    Route::get('/{id}', [GradeTeacherController::class, 'detail'])->middleware('auth:sanctum');
    Route::post('/{id}', [GradeTeacherController::class, 'update'])->middleware('auth:sanctum');
    Route::patch('/{id}/toggle-active', [GradeTeacherController::class, 'toggleActive'])->middleware('auth:sanctum');;
    Route::delete('/{gradeId}/members/{memberId}', [GradeTeacherController::class, 'deleteMember'])->middleware('auth:sanctum');

    Route::prefix('/{gradeId}/student-report')->group(function () {
        Route::get('/', [StudentController::class, 'display'])->middleware('auth:sanctum');
        Route::get('/students/{studentId}', [StudentReportController::class, 'displayTeacher'])->middleware('auth:sanctum');
        Route::get('/{studentReportId}', [StudentReportController::class, 'show'])->middleware('auth:sanctum');
        Route::post('/', [TeacherController::class, 'store'])->middleware('auth:sanctum');
        Route::post('/{studentReportId}', [TeacherController::class, 'update'])->middleware('auth:sanctum');
        Route::delete('/{studentReportId}', [TeacherController::class, 'destroy'])->middleware('auth:sanctum');
        Route::get('/student/{studentId}/semester/{semesterId}', [StudentReportController::class, 'displayStudentReportsBySemester'])->middleware('auth:sanctum');
        Route::get('/semester/{semesterId}', [StudentController::class, 'displayStudentReportsBySemester'])->middleware('auth:sanctum');
    });

    Route::prefix('/{gradeId}/albums')->group(function () {
        Route::get('/', [AlbumController::class, 'showbyGrade'])->middleware('auth:sanctum');
        Route::post('/', [AlbumController::class, 'store'])->middleware('auth:sanctum');
        Route::get('/{albumId}', [AlbumController::class, 'showById'])->middleware('auth:sanctum');
        Route::delete('/{albumId}', [AlbumController::class, 'destroy'])->middleware('auth:sanctum');
    });

    Route::prefix('/{gradeId}/comments')->group(function () {
        Route::post('/', [CommentController::class, 'store'])->middleware('auth:sanctum');
        Route::post('/{commentId}', [CommentController::class, 'update'])->middleware('auth:sanctum');
        Route::delete('/{commentId}', [CommentController::class, 'destroy'])->middleware('auth:sanctum');
        Route::get('/{commentId}', [DisplayController::class, 'detail'])->middleware('auth:sanctum');
        Route::post('/{commentId}/replies', [ReplyController::class, 'store'])->middleware('auth:sanctum');
        Route::post('/{commentId}/replies/{replyId}', [ReplyController::class, 'update'])->middleware('auth:sanctum');
        Route::delete('/{commentId}/replies/{replyId}', [ReplyController::class, 'destroy'])->middleware('auth:sanctum');
    });

    Route::prefix('/{gradeId}/tasks')->group(function (){
        Route::get('/', [TaskDisplayController::class, 'show'])->middleware('auth:sanctum');
        Route::post('/', [TaskController::class, 'store'])->middleware('auth:sanctum');
        Route::post('/{taskId}', [TaskController::class, 'update'])->middleware('auth:sanctum');
        Route::get('/{taskId}', [TaskDisplayController::class, 'detail'])->middleware('auth:sanctum');
        Route::delete('/{taskId}', [TaskController::class, 'destroy'])->middleware('auth:sanctum');
        Route::post('/{taskId}/submit', [TaskStudentController::class, 'store'])->middleware('auth:sanctum');
        Route::post('/{taskId}/submission/{submissionId}', [TaskTeacherController::class, 'correction'])->middleware('auth:sanctum');
        Route::get('/{taskId}/completions', [TaskDisplayController::class, 'completions'])->middleware('auth:sanctum');
    });

    Route::get('/{gradeId}/stream', [StreamController::class, 'index'])->middleware('auth:sanctum');
    Route::get('/{gradeId}/stream/{streamId}', [StreamController::class, 'show'])->middleware('auth:sanctum');
});

Route::prefix('student-report')->group(function () {
    Route::get('/semesters', [StudentReportController::class, 'displaySemesters'])->middleware('auth:sanctum');
});


