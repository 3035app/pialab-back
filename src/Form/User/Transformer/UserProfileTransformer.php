<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Form\User\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use PiaApi\Entity\Oauth\Client;
use Symfony\Bridge\Doctrine\RegistryInterface;
use PiaApi\Entity\Pia\UserProfile;
use PiaApi\Entity\Oauth\User;

class UserProfileTransformer implements DataTransformerInterface
{
    /**
     * @var RegistryInterface
     */
    protected $doctrine;

    /**
     * @param RegistryInterface $doctrine
     */
    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param  UserProfile $profile
     * @return array
     */
    public function transform($profile)
    {
        if($profile instanceof UserProfile) {
          $user = $profile->getUser();

          $array = [
            'id'       => $profile->getId(),
            'user'     => $profile->getUser()->getId(),
            'name'     => $profile->getName(),
            'piaRoles' => $profile->getPiaRoles()
          ];

          return $array;
        }

        return $profile;
    }

    /**
     * @param  string $value
     * @return UserProfile
     */
    public function reverseTransform($value)
    {
        $profile = new UserProfile();

        $userRepository = $this->doctrine->getRepository(User::class);
        $user = $userRepository->find($value['user']);

        $profileRepository = $this->doctrine->getRepository(UserProfile::class);

        if($value['id']) {
          $profile = $profileRepository->find($value['id']);
        }

        $profile->setName($value['name']);
        $profile->setPiaRoles($value['piaRoles']);
        $profile->setUser($user);
        $user->setProfile($profile);

        return $profile;
    }
}
