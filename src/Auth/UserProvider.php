<?php

namespace PiaApi\Auth;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use PiaApi\Entity\User;
use Symfony\Bridge\Doctrine\RegistryInterface;

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

    public function loadUserByUsername($username)
    {
        return $this->doctrine->getRepository(User::class)->findOneBy([
            'email' => $username,
        ]);
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
