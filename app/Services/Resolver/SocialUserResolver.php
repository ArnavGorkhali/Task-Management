<?php

namespace App\Services\Resolver;

use App\Repositories\User\UserRepository;
use App\Services\SocialAccountsService;
use Exception;
use Coderello\SocialGrant\Resolvers\SocialUserResolverInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Laravel\Socialite\Facades\Socialite;

class SocialUserResolver implements SocialUserResolverInterface
{

    /**
     * Resolve user by provider credentials.
     *
     * @param string $provider
     * @param string $accessToken
     *
     * @return Authenticatable|null
     */
    public function resolveUserByProviderCredentials(string $provider, string $accessToken): ?Authenticatable
    {
        $providerUser = null;
        try {
            $providerUser = Socialite::driver($provider)->stateless()->userFromToken($accessToken);
        } catch (Exception $exception) {
        }

        if ($providerUser) {
            $userRepository = new UserRepository();
//            dd($userRepository->socialLogin($providerUser, $provider));
            return $userRepository->socialLogin($providerUser, $provider);
//            return (new SocialAccountsService())->findOrCreate($providerUser, $provider);
        }

        return null;
    }
}
