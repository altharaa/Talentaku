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
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }    

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = $request->user();
            $token = $user->createToken($request->input('token_name', 'auth_token'));
            $roles = $user->roles()->pluck('name')->toArray();

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'token' => $token->plainTextToken,
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
