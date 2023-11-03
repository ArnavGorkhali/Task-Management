<?php
namespace App\Repositories\User;

use Laravel\Socialite\Two\User as ProviderUser;

interface UserRepositoryInterface
{
    public function socialLogin(ProviderUser $providerUser, string $provider);

    public function store($request);

    public function update($request, $user);

    public function delete($user);
}
