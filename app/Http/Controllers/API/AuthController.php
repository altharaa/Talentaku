<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = $request->user();
            $tokenName = $request->input('token_name', 'auth_token');
            $token = $user->createToken($tokenName)->plainTextToken;
            $roles = $user->roles()->pluck('name')->toArray();

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'token' => $token,
                    'user' => $user->name,
                    'role' => $roles,
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Login failed',
        ], 401);
    }

    public function view(Request $request)
    {
        $user = $request->user();
        $roles = $user->roles()->pluck('name')->toArray();

        return response()->json([
            'user' => $user,
            'roles' => $roles
        ]);
    }

    public function destroy(Request $request)
    {
        $user = $request->user();

        if ($user) {
            $user->tokens()->delete();
            return response()->json(['message' => 'Logged out successfully']);
        }

        return response()->json(['error' => 'User not authenticated'], 401);
    }
}
