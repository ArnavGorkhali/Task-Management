<?php

namespace App\Services;

use App\Models\User;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;

class BearerTokenResponse extends \League\OAuth2\Server\ResponseTypes\BearerTokenResponse
{
    /**
     * Add custom fields to your Bearer Token response here, then override
     * AuthorizationServer::getResponseType() to pull in your version of
     * this class rather than the default.
     *
     * @param AccessTokenEntityInterface $accessToken
     *
     * @return array
     */
    protected function getExtraParams(AccessTokenEntityInterface $accessToken): array
    {
        $user_id = $this->accessToken->getUserIdentifier();
        $user = User::withoutGlobalScopes()->where('deleted_at', null)->find($user_id);
        return [
            'user' => $user,
        ];
    }
}
