<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $request->validated();
        $user = User::where('username', $request->username)->first();
        User::query()->where("username", $request->username)->update(["fcm_token" => $request->fcm_token ?? null]);
        $token = $user->createToken('talentaku')->plainTextToken;
        $userResource = new UserResource($user);

        return response([
            'data' => $userResource,
            'token' => $token,
            'fcm_token' => $request->fcm_token,
        ], 200);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user) {
            $user->tokens()->delete();
            return response()->json(['message' => 'Logged out successfully']);
        }

        return response()->json(['error' => 'User not authenticated'], 401);
    }
}
