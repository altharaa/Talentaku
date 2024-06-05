<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\InformationController;
use App\Http\Controllers\API\ProgramController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
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

Route::prefix('/user')->group(function () {
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

Route::get('/information',[InformationController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
   Route::prefix('/grades')->group(function () {
        Route::get('/', 'App\Http\Controllers\API\GradeController@show');
        Route::post('/add', 'App\Http\Controllers\API\GradeController@store');
        Route::put('/{id}', 'App\Http\Controllers\API\GradeController@update');
        Route::patch('{id}/toggle-active', 'App\Http\Controllers\API\GradeController@toggleActive' );
        Route::post('/join', 'App\Http\Controllers\API\GradeController@join');
   });
});
