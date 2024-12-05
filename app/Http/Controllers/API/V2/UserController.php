<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        return new UserResource($user);
    }
    public function updatePhoto(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($user->photo) {
                $oldPhotoPath = storage_path('app/public/profile/' . $user->photo);
                if (file_exists($oldPhotoPath)) {
                    @unlink($oldPhotoPath); 
                }
            }
    
            $imagePath = $request->file('photo')->store('public/profile');
            $user->photo = $imagePath;
        }
    
        $user->save(); 
    
        return $this->resUpdateData(new UserResource($user));
    }
    public function updatePassword(Request $request)
    {
        $user = $request->user(); 

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed', 
        ]);

        if (!Hash::check($request->input('current_password'), $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect.',
            ], 422);
        }
    
        if (Hash::check($request->input('new_password'), $user->password)) {
            return response()->json([
                'message' => 'The new password must be different from the current password.',
            ], 422);
        }

        $user->password = Hash::make($request->input('new_password'));

        if (method_exists($user, 'tokens')) {
            $user->tokens()->delete(); 
        }

        $user->save(); 

        $newToken = $user->createToken('talentaku')->plainTextToken;

        $response = [
            'message' => 'Password updated successfully.',
            'token' => $newToken, 
            'user' => new UserResource($user), 
        ];

        return $this->resUpdateData($response);
    }
}
