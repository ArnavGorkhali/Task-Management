<?php

namespace App\Repositories\Profile;

use App\Services\FileUploadService;
use http\Client\Curl\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileRepository implements ProfileRepositoryInterface
{
    public function getProfile()
    {
        return Auth::user();
    }

    public function changePassword($request)
    {
        $user = $this->getProfile();
        if ($user->password ? (Hash::check($request->input('current_password'), $user->password)) : true) {
            try {
                $user->password = Hash::make($request->input('new_password'));
                $user->save();
                return $user;
            } catch (\Exception $e) {
                throw new \Exception('Password not changed.');
            }
        } else {
            throw ValidationException::withMessages(['current_password' => 'Current password is incorrect!']);
        }

    }

    public function update($request)
    {
        $user = Auth::user();
        $data = $request->all();
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'profile_pic' => $request->profile_pic ? (new FileUploadService())->uploadImage($request->profile_pic, $user)
                : $user->getAttributes()['profile_pic'],
        ]);
        return $user;
    }
}
