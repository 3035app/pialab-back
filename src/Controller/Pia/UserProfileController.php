<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Controller\Pia;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use FOS\RestBundle\View\View;
use PiaApi\Entity\Pia\UserProfile;

class UserProfileController extends RestController
{
    /**
     * @FOSRest\Get("/profile")
     *
     * @return array
     */
    public function profileAction(UserInterface $user = null)
    {
        $this->canAccessRouteOr403();

        return $this->view($user->getProfile(), Response::HTTP_OK);
    }

    protected function getEntityClass()
    {
        return UserProfile::class;
    }
}
