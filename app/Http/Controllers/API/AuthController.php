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
        // Validate the request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Attempt to log in the user
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
           // Retrieve the authenticated user
           $user = $request->user();
           // Create token
           $token = $user->createToken($request->input('token_name', 'auth_token'));
            
            return response()->json([
                'success' => true,
                'message' => 'Berhasil',
                'data' => [
                    'token' => $token->plainTextToken,
                    'user' => [
                        'name' => $request->user()->name,
                        // 'address' => $request->user()->address,
                        // 'birth_date' => $request->user()->birth_date,
                        // 'photo' => $request->user()->photo,
                    ],
                ],
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Gagal',
                'data' => null,
            ]);
        }
    }

    public function destroy(Request $request) 
    {
        // Retrieve the authenticated user
        $user = $request->user();
        
        // Revoke all tokens for the user
        $user->currentAccessToken()->delete();
        
        // Optionally, revoke the token that was used to authenticate the current request
        // $request->user()->currentAccessToken()->delete();

        // Optionally, revoke a specific token by token ID
        // $tokenId = 1; // Replace with the actual token ID you want to revoke
        // $user->tokens()->where('id', $tokenId)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }
}
