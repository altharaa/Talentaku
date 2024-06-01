<?php

use App\Http\Controllers\MemberController;
use App\Models\Information;
use App\Models\Member;
use App\Models\User;
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

Route::get('/', function () {
    echo password_hash('moza1234', PASSWORD_BCRYPT);
});

// Route::get('/test', function () {
//     $info = User::find(1);
//    $list = [1,2];

//    $info->roles()->sync($list);

//     echo 'berhasil';
// });

// Route::post('/grades/{gradeId}/students', [MemberController::class, 'store']);
