<?php

use App\Http\Controllers\API\AlbumController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\Grade\StudentController as GradeStudentController;
use App\Http\Controllers\API\Grade\TeacherController as GradeTeacherController;
use App\Http\Controllers\API\InformationController;
use App\Http\Controllers\API\ProgramController;
use App\Http\Controllers\API\StudentReport\StudentController;
use App\Http\Controllers\API\StudentReport\StudentReportController;
use App\Http\Controllers\API\StudentReport\TeacherController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth:sanctum');
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
        Route::get('/students/{studentId}', [TeacherController::class, 'display'])->middleware('auth:sanctum');
        Route::get('/{studentReportId}', [StudentController::class, 'show'])->middleware('auth:sanctum');
        Route::post('/', [TeacherController::class, 'store'])->middleware('auth:sanctum');
        Route::post('/{studentReportId}', [TeacherController::class, 'update'])->middleware('auth:sanctum');
        Route::delete('/{studentReportId}', [TeacherController::class, 'destroy'])->middleware('auth:sanctum');
        Route::get('/students/{studentId}/{semester}', [TeacherController::class, 'displayStudentReportsBySemester'])->middleware('auth:sanctum');
        Route::get('/students/{studentId}/{semester}', [StudentController::class, 'displayStudentReportsBySemester'])->middleware('auth:sanctum');
    });

    Route::prefix('/{gradeId}/albums')->group(function () {
        Route::get('/', [AlbumController::class, 'index'])->middleware('auth:sanctum');
        Route::post('/', [AlbumController::class, 'store'])->middleware('auth:sanctum');
        Route::get('/{albumId}', [AlbumController::class, 'show'])->middleware('auth:sanctum');
        Route::post('/{albumId}', [AlbumController::class, 'update'])->middleware('auth:sanctum');
        Route::delete('/{albumId}', [AlbumController::class, 'destroy'])->middleware('auth:sanctum');
    });
});

Route::prefix('student-report')->group(function () {
    Route::get('/semesters', [StudentReportController::class, 'semesters'])->middleware('auth:sanctum');
    Route::get('/points', [StudentReportController::class, 'points'])->middleware('auth:sanctum');
});


