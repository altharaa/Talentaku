<?php

use App\Http\Controllers\API\AlbumController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\Comment\CommentController;
use App\Http\Controllers\API\Comment\DisplayController;
use App\Http\Controllers\API\Comment\ReplyController;
use App\Http\Controllers\API\Grade\StudentController as GradeStudentController;
use App\Http\Controllers\API\Grade\TeacherController as GradeTeacherController;
use App\Http\Controllers\API\GradeController;
use App\Http\Controllers\API\InformationController;
use App\Http\Controllers\API\ProgramController;
use App\Http\Controllers\API\StreamController;
use App\Http\Controllers\API\StudentReportController;
use App\Http\Controllers\API\StudentReportDisplayController;
use App\Http\Controllers\API\StudentReportDisplayForStudentController;
use App\Http\Controllers\API\StudentReportDisplayForTeacherController;
use App\Http\Controllers\API\StudentReportSemesterController;
use App\Http\Controllers\API\TaskController;
use App\Http\Controllers\API\TaskDisplayController;
use App\Http\Controllers\API\TaskSubmissionController;
use App\Http\Controllers\API\TaskSubmissionCorrectionController;
use App\Http\Controllers\API\TaskSubmissionDisplayController;
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
    Route::post('/', [GradeController::class, 'store'])->middleware('auth:sanctum');
    Route::post('/{gradeId}', [GradeController::class, 'update'])->middleware('auth:sanctum');
    Route::delete('/{gradeId}', [GradeController::class, 'delete'])->middleware('auth:sanctum');
//    Route::post('/join', [GradeStudentController::class, 'join'])->middleware('auth:sanctum');
//    Route::get('/teacher', [GradeTeacherController::class, 'index'])->middleware('auth:sanctum');
//    Route::get('/student', [GradeStudentController::class, 'index'])->middleware('auth:sanctum');
//    Route::get('/{id}', [GradeTeacherController::class, 'detail'])->middleware('auth:sanctum');
//    Route::patch('/{id}/toggle-active', [GradeTeacherController::class, 'toggleActive'])->middleware('auth:sanctum');;
//    Route::delete('/{gradeId}/members/{memberId}', [GradeTeacherController::class, 'deleteMember'])->middleware('auth:sanctum');

    Route::prefix('/{gradeId}/student-report')->group(function () {
        Route::post('/', [StudentReportController::class, 'store'])->middleware('auth:sanctum');
        Route::post('/{studentReportId}', [StudentReportController::class, 'update'])->middleware('auth:sanctum');
        Route::delete('/{studentReportId}', [StudentReportController::class, 'destroy'])->middleware('auth:sanctum');
        Route::get('/student/{studentId}', [StudentReportDisplayForTeacherController::class, 'displayAll'])->middleware('auth:sanctum');
        Route::get('/student/{studentId}/semester/{semesterId}', [StudentReportDisplayForTeacherController::class, 'displayBySemester'])->middleware('auth:sanctum');
        Route::get('/student', [StudentReportDisplayForStudentController::class, 'displayAll'])->middleware('auth:sanctum');
        Route::get('/student/semester/{semesterId}', [StudentReportDisplayForStudentController::class, 'displayBySemester'])->middleware('auth:sanctum');
        Route::get('/{studentReportId}', [StudentReportDisplayController::class, 'detail'])->middleware('auth:sanctum');
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
        Route::get('/', [TaskDisplayController::class, 'showByGrade'])->middleware('auth:sanctum');
        Route::post('/', [TaskController::class, 'store'])->middleware('auth:sanctum');
        Route::post('/{taskId}', [TaskController::class, 'update'])->middleware('auth:sanctum');
        Route::get('/{taskId}', [TaskDisplayController::class, 'showById'])->middleware('auth:sanctum');
        Route::delete('/{taskId}', [TaskController::class, 'destroy'])->middleware('auth:sanctum');
        Route::post('/{taskId}/submit', [TaskSubmissionController::class, 'store'])->middleware('auth:sanctum');
        Route::post('/{taskId}/submission/{submissionId}', [TaskSubmissionCorrectionController::class, 'correction'])->middleware('auth:sanctum');
        Route::get('/{taskId}/completions', [TaskSubmissionDisplayController::class, 'completions'])->middleware('auth:sanctum');
        Route::get('/{taskId}/completions/{submissionId}', [TaskSubmissionDisplayController::class, 'show'])->middleware('auth:sanctum');
        Route::get('/{taskId}/completions-with-scores', [TaskSubmissionDisplayController::class, 'completionsWithScores'])->middleware('auth:sanctum');
    });

    Route::get('/{gradeId}/stream', [StreamController::class, 'index'])->middleware('auth:sanctum');
    Route::get('/{gradeId}/stream/{streamId}', [StreamController::class, 'show'])->middleware('auth:sanctum');
});

Route::get('student-report/semesters', [StudentReportSemesterController::class, 'displaySemesters'])->middleware('auth:sanctum');
