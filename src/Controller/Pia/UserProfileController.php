<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Controller\Pia;

use FOS\RestBundle\Controller\Annotations as FOSRest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use PiaApi\Entity\Pia\UserProfile;
use Swagger\Annotations as Swg;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class UserProfileController extends RestController
{
    /**
     * Shows the current User's profile.
     *
     * @Swg\Tag(name="UserProfile")
     *
     * @FOSRest\Get("/profile")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns the current User's profile",
     *     @Swg\Schema(
     *         type="array",
     *         @Swg\Items(ref=@Nelmio\Model(type=UserProfile::class, groups={"Default"}))
     *     )
     * )
     *
     * @return array
     */
    public function profileAction(UserInterface $user = null)
    {
        $this->canAccessRouteOr403();

        return $this->view($user->getProfile(), Response::HTTP_OK);
    }

    /**
     * Shows the current User's profile.
     *
     * @Swg\Tag(name="UserProfile")
     *
     * @FOSRest\Get("/profile/structures")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns the current User's profile",
     *     @Swg\Schema(
     *         type="array",
     *         @Swg\Items(ref=@Nelmio\Model(type=UserProfile::class, groups={"Default"}))
     *     )
     * )
     *
     * @return array
     */
    public function profileStructuresAction(UserInterface $user = null)
    {
        return $this->profileAction($user);
    }

    protected function getEntityClass()
    {
        return UserProfile::class;
    }
}
