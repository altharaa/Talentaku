<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\User;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $roles = $user->roles()->pluck('name')->toArray();
    
        if (in_array('Murid SD', $roles) || in_array('Murid KB', $roles)) {
            $grades = $user->members()->with('grade')->get()->pluck('grade.name')->toArray();
        } elseif (in_array('Guru SD', $roles) || in_array('Guru KB', $roles)) {
            $grades = Grade::where('teacher_id', $user->id)->pluck('name')->toArray();
        } else {
            return response()->json([
                'message' => 'User doesn\'t have a valid role'
            ], 404);
        }
    
        if (empty($grades)) {
            return response()->json([
                'user' => $user,
                'roles' => $roles,
                'grades' => 'User didn\'t have any class'
            ]);
        }
        
        return response()->json([
            'user' => $user,
            'roles' => $roles,
            'grades' => $grades
        ]);
    }
    public function updatePhoto(Request $request)
    {
        $id = $request->user()->id;
        $user = User::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'photo' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validatedData = $validator->validated();

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo')->storePublicly('photos', 'public');
            $validatedData['photo'] = Storage::url($photo);
        }

        $user->fill($validatedData);

        $user->save();

        return response()->json([
            'status' => 'Photo updated successfully',
            'data' => [
                $user->id,
                $user->name,
                $user->photo,
            ]
        ], 200);
    }
    
    public function updatePassword(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required',
                'new_password' => 'required|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = $request->user();
            $currentPassword = $request->input('current_password');
            $newPassword = $request->input('new_password');

            if (!Hash::check($currentPassword, $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Current password is incorrect',
                ], 422);
            }

            $user->password = Hash::make($newPassword);
            $user->save();

            return response()->json([
                'status' => 'Password updated successfully',
            ], 200);
        }
    
}
