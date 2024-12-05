<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user) {
            return response()->json([
                'message' => 'The username is incorrect.',
            ], 401);
        }
    
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'The password is incorrect.',
            ], 401);
        }

        if( $request->fcm_token)
        {
            User::query()->where("username", $request->username)->update(["fcm_token" => $request->fcm_token]);
        }

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

        if($user)
        {
            $user->tokens()->delete();
            return response()->json(['message' => 'Logged out successfully']);
        }

        return response()->json(['error' => 'User not authenticated'], 401);
    }
}
