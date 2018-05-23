<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Controller\Pia;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Bridge\Doctrine\RegistryInterface;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use FOS\RestBundle\View\View;
use PiaApi\Entity\Oauth\User;
use PiaApi\Entity\Pia\UserProfile;

class UserProfileController extends RestController
{
    protected $repository;

    public function __construct(RegistryInterface $doctrine) {
      $this->repository = $doctrine->getRepository(UserProfile::class);
    }

    /**
     * @FOSRest\Get("/profile")
     *
     * @return array
     */
    public function profileAction(UserInterface $user = null)
    {
        $this->canAccessRouteOr304();

        return $this->view($user->getProfile(), Response::HTTP_OK);
    }

    protected function getEntityClass()
    {
        return UserProfile::class;
    }
}
