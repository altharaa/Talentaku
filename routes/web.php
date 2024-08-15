<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

function serveFile($path)
{
    if (!File::exists($path)) {
        abort(404);
    }

    return Response::file($path);
}

$imageRoutes = [
    'profile' => 'profile',
    'album-media' => 'album-media',
    'task' => 'tasks',
    'task-submission' => 'task-submissions',
    'student-report' => 'student-reports',
    'program' => 'programs',
    'announcement_media' => 'announcement-media'
];

foreach ($imageRoutes as $route => $folder) {
    Route::get("/image/{$route}/{filename}", fn ($filename) =>
    serveFile(storage_path("app/public/{$folder}/{$filename}"))
    );
}
