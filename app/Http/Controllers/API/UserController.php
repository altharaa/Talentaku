<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserPasswordRequest;
use App\Http\Requests\UserPhotoRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function show(Request $request)
    {
        $user = $request->user();
    
        if (!$user->roles()->exists()) {
            return response()->json([
                'message' => 'User doesn\'t have a valid role'
            ], 404);
        }

        return new UserResource($user);
    }
    public function updatePhoto(UserPhotoRequest $request)
    {
        $userId = $request->user()->id;
        $user = User::findOrFail($userId);

        if ($request->hasFile('photo')) {
            $imagePath = $request->file('photo')->store('public/photos');
            $fileName = basename($imagePath);
            $user['photo'] = $fileName;
        }

        $user->save();

        return $this->resStoreData($user);
    }
    
    public function updatePassword(UserPasswordRequest $request)
    {
        $user = $request->user();
        $currentPassword = $request->input('current_password');
        $newPassword = $request->input('new_password');
    
        if (!Hash::check($currentPassword, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Current password is incorrect',
            ], 422);
        }
    
        if (Hash::check($newPassword, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'New password must be different from the current password',
            ], 422);
        }
    
        $user->password = Hash::make($newPassword);
        if (method_exists($user, 'tokens')) {
            $user->tokens()->delete();
        }
        $user->save();
    
        return $this->resStoreData($user);
    }  
}
