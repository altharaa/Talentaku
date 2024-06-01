<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $roles = $user->roles()->pluck('name')->toArray();

        return response()->json([
            'user' => $user,
            'roles' => $roles
        ]);
    }

    
}
