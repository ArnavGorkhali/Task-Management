<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Two\User as ProviderUser;

class UserRepository implements UserRepositoryInterface
{

    public function socialLogin(ProviderUser $providerUser, string $provider)
    {
        DB::beginTransaction();
        try {
            $user = User::where('provider_id', $providerUser->getId())->first();
            if (!$user) {
                if ($providerUser->getEmail()) {
                    $user = User::where('email', $providerUser->getEmail())->first();
                    if ($user) {
                        $this->updateSocialUser($user, $providerUser, $provider);
                    } else {
                        $user = $this->createSocialUser($providerUser, $provider);
                    }
                } else {
                    $user = $this->createSocialUser($providerUser, $provider);
                }
            }
            $user->save();
            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
        }
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'permission' => $data['permission'],
                'role' => $data['role'],
                'password' => Hash::make($data['password']),
                'phone' => $data['phone'],
                'profile_pic' => $request->profile_pic ? (new FileUploadService())->uploadImage($request->profile_pic) : null,
            ]);
            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
        }
    }

    public function update($request, $user)
    {
        $data = $request->all();
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'profile_pic' => $request->profile_pic ? (new FileUploadService())->uploadImage($request->profile_pic, $user) : $user->profile_pic,
        ]);
        return $user;
    }

    public function delete($user)
    {
        $user->delete();
        return $user;
    }

    private function createSocialUser($social_user, $provider)
    {
        $user = User::create([
            'name' => $social_user->getName(),
            'email' => $social_user->getEmail(),
            'provider_id' => $social_user->getId(),
            'provider' => $provider,
            'profile_pic' => $social_user->getAvatar()
                ? (new FileUploadService())->uploadImageUrl($social_user->getAvatar())
                : null
        ]);
        $user->save();
        $user->markEmailAsVerified();
        return $user;
    }

    private function updateSocialUser($user, $social_user, $provider)
    {
        $user->update([
            'name' => $social_user->getName(),
            'email' => $social_user->getEmail(),
            'provider_id' => $social_user->getId(),
            'provider' => $provider,
            'profile_pic' => $social_user->getAvatar()
                ? (new FileUploadService())->uploadImageUrl($social_user->getAvatar(), $user)
                : $user->getAttributes()['profile_pic']
        ]);
    }
}
