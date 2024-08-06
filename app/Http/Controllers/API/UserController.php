<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserPasswordRequest;
use App\Http\Requests\UserPhotoRequest;
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
    public function updatePhoto(UserPhotoRequest $request)
    {
        $user = $request->user();
        if ($request->hasFile('photo')) {
            $imagePath = $request->file('photo')->store('public/profile');
            $fileName = basename($imagePath);
            $user->photo = $fileName;
        }
        $user->save();
        return $this->resStoreData(new UserResource($user));
    }
    public function updatePassword(UserPasswordRequest $request)
    {
        $user = $request->user();
        $user->password = Hash::make($request->input('new_password'));
        if (method_exists($user, 'tokens')) {
            $user->tokens()->delete();
        }
        $user->save();
        return $this->resStoreData(new UserResource($user));
    }
}
