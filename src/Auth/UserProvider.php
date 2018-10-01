<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Auth;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use PiaApi\Entity\Oauth\User;
use Symfony\Bridge\Doctrine\RegistryInterface;
use OAuth2\OAuth2ServerException;
use Symfony\Component\HttpFoundation\Response;

class UserProvider implements UserProviderInterface
{
    /**
     * @var RegistryInterface
     */
    private $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function loadUserByUsername($usernameOrEmail)
    {
        $user = $this->doctrine->getRepository(User::class)->findUserByUsernameOrEmail($usernameOrEmail);

        if (
            !$user->isEnabled() ||
            $user->isLocked() ||
            $user->getExpirationDate() < new \DateTime()
        ) {
            throw new OAuth2ServerException(Response::HTTP_FORBIDDEN, 'The account is locked and/or expired');
        }

        return $user;
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        return $this->doctrine->getRepository(User::class)->find($user->getId());
    }

    public function supportsClass($class)
    {
        return $class === User::class;
    }
}
