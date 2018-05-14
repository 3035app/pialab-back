<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Auth;

use FOS\OAuthServerBundle\Storage\OAuthStorage as BaseOAuthStorage;
use OAuth2\Model\IOAuth2Client;
use FOS\OAuthServerBundle\Model\ClientManagerInterface;
use FOS\OAuthServerBundle\Model\AccessTokenManagerInterface;
use FOS\OAuthServerBundle\Model\RefreshTokenManagerInterface;
use FOS\OAuthServerBundle\Model\AuthCodeManagerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use PiaApi\Entity\Oauth\User;
use OAuth2\OAuth2ServerException;
use Symfony\Component\HttpFoundation\Response;

class OAuthStorage extends BaseOAuthStorage
{
    public function __construct(
        ClientManagerInterface $clientManager,
        AccessTokenManagerInterface $accessTokenManager,
        RefreshTokenManagerInterface $refreshTokenManager,
        AuthCodeManagerInterface $authCodeManager,
        UserProviderInterface $userProvider = null,
        EncoderFactoryInterface $encoderFactory = null,
        UserProvider $piaUserProver
    ) {
        parent::__construct(
            $clientManager,
            $accessTokenManager,
            $refreshTokenManager,
            $authCodeManager,
            $userProvider,
            $encoderFactory
        );

        $this->userProvider = $piaUserProver;
    }

    public function createAccessToken($tokenString, IOAuth2Client $client, $data, $expires, $scope = null)
    {
        /** @var User $user */
        $user = $data;

        if ($client !== $user->getApplication()) {
            throw new OAuth2ServerException(Response::HTTP_BAD_REQUEST, sprintf('User « %s » cannot access application « %s »', $user->getUsername(), $client->getName()));
        }
        parent::createAccessToken($tokenString, $client, $data, $expires, $scope);
    }
}
