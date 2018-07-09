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
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as Swg;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use FOS\RestBundle\View\View;
use PiaApi\Entity\Oauth\User;

class UserController extends RestController
{
    /**
     * @Swg\Tag(name="User")
     *
     * @FOSRest\Get("/users")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns all users",
     *     @Swg\Schema(
     *         type="array",
     *         @Swg\Items(ref=@Nelmio\Model(type=User::class, groups={"Default"}))
     *     )
     * )
     *
     * @Security("is_granted('CAN_SHOW_USER')")
     *
     * @return array
     */
    public function listAction(Request $request)
    {
        return $this->view([], Response::HTTP_OK);
    }

    /**
     * @Swg\Tag(name="User")
     *
     * @FOSRest\Get("/users/{id}", requirements={"id"="\d+"})
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns one User by its id",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=User::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_SHOW_USER')")
     *
     * @return array
     */
    public function showAction(Request $request, $id)
    {
        return $this->view([], Response::HTTP_OK);
    }

    /**
     * @Swg\Tag(name="User")
     *
     * @FOSRest\Post("/users")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Creates a user",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=User::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_CREATE_USER')")
     *
     * @return array
     */
    public function createAction(Request $request)
    {
        return $this->view([], Response::HTTP_OK);
    }

    /**
     * @Swg\Tag(name="User")
     *
     * @FOSRest\Put("/users/{id}", requirements={"id"="\d+"})
     *
     * @Swg\Response(
     *     response=200,
     *     description="Update a user",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=User::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_EDIT_USER')")
     *
     * @return array
     */
    public function updateAction(Request $request, $id)
    {
        return $this->view([], Response::HTTP_OK);
    }

    /**
     * @Swg\Tag(name="User")
     *
     * @FOSRest\Delete("/users/{id}", requirements={"id"="\d+"})
     *
     * @Swg\Response(
     *     response=200,
     *     description="Delete a user",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=User::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_DELETE_USER')")
     *
     * @return array
     */
    public function deleteAction(Request $request, $id)
    {
        return $this->view([], Response::HTTP_OK);
    }

    protected function getEntityClass()
    {
        return User::class;
    }
}
