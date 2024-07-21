<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\User;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

    public function show(Request $request)
    {
        $user = $request->user();

        $userData = User::select('id', 'name', 'email', 'identification_number', 'address', 'photo')
                ->selectRaw("CONCAT(place_of_birth, ', ', DATE_FORMAT(birth_date, '%Y-%m-%d')) AS birth_information")
                ->where('id', $user->id) 
                ->first();

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
                'user' => $userData,
                'roles' => $roles,
                'grades' => 'User didn\'t have any class'
            ]);
        }
        
        return response()->json([
            'user' => $userData,
            'roles' => $roles,
            'grades' => $grades
        ]);
    }
    public function updatePhoto(Request $request)
    {
        $id = $request->user()->id;
        $user = User::findOrFail($id);
        
        $photo = $request->file('photo');
        
        $validator = Validator::make(['photo' => $photo], [
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        if ($request->hasFile('photo')) {
            $imagePath = $request->file('photo')->store('public');
            $user['photo'] = url(Storage::url($imagePath));
        }

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
