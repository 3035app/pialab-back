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
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use Pagerfanta\Pagerfanta;
use PiaApi\Entity\Oauth\Client;
use PiaApi\Entity\Oauth\User;
use PiaApi\Entity\Pia\Structure;
use PiaApi\Services\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as Swg;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use PiaApi\DataHandler\RequestDataHandler;
use PharIo\Manifest\Application;

class UserController extends RestController
{
    /**
     * @var UserService
     */
    private $userService;

    public function __construct(
        PropertyAccessorInterface $propertyAccessor,
        UserService $userService
    ) {
        parent::__construct($propertyAccessor);
        $this->userService = $userService;
    }

    /**
     * Lists all users.
     *
     * @Swg\Tag(name="User")
     *
     * @FOSRest\Get("/users")
     *
     * @Swg\Parameter(
     *     name="Authorization",
     *     in="header",
     *     type="string",
     *     required=true,
     *     description="The API token. e.g.: Bearer <TOKEN>"
     * )
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
        $criteria = array_merge($this->extractCriteria($request), ['limit' => 20, 'page' => 1]);

        /* @var Pagerfanta $userPager */
        $userPager;
        $structure = null;

        if ($this->isGranted('CAN_MANAGE_ONLY_OWNED_USERS')) {
            $structure = $this->getUser()->getStructure();
        }

        if ($structure !== null) {
            $userPager = $this->getDoctrine()
                ->getRepository($this->getEntityClass())
                ->getPaginatedUsersByStructure($structure, $criteria['limit'], $criteria['page']);
        } else {
            $userPager = $this->getDoctrine()
                ->getRepository($this->getEntityClass())
                ->getPaginatedUsers($criteria['limit'], $criteria['page']);
        }

        return $this->view($userPager->getCurrentPageResults()->getArrayCopy(), Response::HTTP_OK);
    }

    /**
     * Shows one User by its ID.
     *
     * @Swg\Tag(name="User")
     *
     * @FOSRest\Get("/users/{id}", requirements={"id"="\d+"})
     *
     * @Swg\Parameter(
     *     name="Authorization",
     *     in="header",
     *     type="string",
     *     required=true,
     *     description="The API token. e.g.: Bearer <TOKEN>"
     * )
     * @Swg\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     required=true,
     *     description="The ID of the User"
     * )
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns one User",
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
        $entity = $this->getRepository()->find($id);

        if ($entity === null) {
            return $this->view($entity, Response::HTTP_NOT_FOUND);
        }

        $this->canAccessResourceOr403($entity);

        return $this->view($entity, Response::HTTP_OK);
    }

    /**
     * Creates a User.
     *
     * @Swg\Tag(name="User")
     *
     * @FOSRest\Post("/users")
     *
     * @Swg\Parameter(
     *     name="Authorization",
     *     in="header",
     *     type="string",
     *     required=true,
     *     description="The API token. e.g.: Bearer <TOKEN>"
     * )
     * @Swg\Parameter(
     *     name="User",
     *     in="body",
     *     required=false,
     *     @Swg\Schema(
     *         type="object",
     *         required={"email", "password"},
     *         @Swg\Property(property="email", type="string"),
     *         @Swg\Property(property="password", type="string"),
     *         @Swg\Property(property="roles", type="array", @Swg\Items(type="string")),
     *         @Swg\Property(property="sendResettingEmail", type="boolean"),
     *         @Swg\Property(property="structure", type="integer"),
     *         @Swg\Property(property="application", type="integer")
     *     ),
     *     description="The User content"
     * )
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns the newly created User",
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
        $structure = $this->getRepository(Structure::class)->find($request->get('structure', -1));
        $application = $this->getRepository(Client::class)->find($request->get('application', -1));

        $user = $this->userService->createUser(
            $request->get('email'),
            $request->get('password'),
            $structure,
            $application
        );

        foreach ($request->get('roles', []) as $role) {
            if ($role !== 'ROLE_SUPER_ADMIN') { // Never create a super admin via API
                $user->addRole($role);
            }
        }

        //a ROLE_ADMIN (which contains CAN_MANAGE_ONLY_OWNED_USERS) must have a structure
        if ($structure === null && $user->hasRole('ROLE_ADMIN')) {
            // throw new \DomainException('A Functional Administrator must be assigned to a Structure'); <= Removed exception because, in prod env, a 500 error page, without the message, will be displayed
            return $this->view(['A Functional Administrator must be assigned to a Structure'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $this->persist($user);

        if ($request->get('sendResettingEmail') === true) {
            $this->userService->sendResettingEmail($user);
        }

        return $this->view($user, Response::HTTP_OK);
    }

    /**
     * Updates a User.
     *
     * @Swg\Tag(name="User")
     *
     * @FOSRest\Put("/users/{id}", requirements={"id"="\d+"})
     *
     * @Swg\Parameter(
     *     name="Authorization",
     *     in="header",
     *     type="string",
     *     required=true,
     *     description="The API token. e.g.: Bearer <TOKEN>"
     * )
     * @Swg\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     required=true,
     *     description="The ID of the User"
     * )
     * @Swg\Parameter(
     *     name="User",
     *     in="body",
     *     required=true,
     *     @Swg\Schema(
     *         type="object",
     *         @Swg\Property(property="username", type="string"),
     *         @Swg\Property(property="email", type="string"),
     *         @Swg\Property(property="password", type="string"),
     *         @Swg\Property(property="enabled", type="boolean"),
     *         @Swg\Property(property="locked", type="boolean"),
     *         @Swg\Property(property="expiration_date", type="string"),
     *         @Swg\Property(property="roles", type="array", @Swg\Items(type="string")),
     *         @Swg\Property(property="structure", type="object", @Swg\Property(property="id", type="number")),
     *         @Swg\Property(property="application", type="object", @Swg\Property(property="id", type="number"))
     *     ),
     *     description="The User content"
     * )
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns the updated User",
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
        $user = $this->getResource($id);
        $this->canAccessResourceOr403($user);

        $updatableAttributes = [
            'username'        => RequestDataHandler::TYPE_STRING,
            'email'           => RequestDataHandler::TYPE_STRING,
            'enabled'         => RequestDataHandler::TYPE_BOOL,
            'password'        => RequestDataHandler::TYPE_STRING,
            'roles'           => RequestDataHandler::TYPE_ARRAY,
            'expiration_date' => \DateTime::class,
            'locked'          => RequestDataHandler::TYPE_BOOL,
            'structure'       => Structure::class,
            'application'     => Client::class,
        ];

        $this->mergeFromRequest($user, $updatableAttributes, $request);

        $this->update($user);

        return $this->view($user, Response::HTTP_OK);
    }

    /**
     * Deletes a User.
     *
     * @Swg\Tag(name="User")
     *
     * @FOSRest\Delete("/users/{id}", requirements={"id"="\d+"})
     *
     * @Swg\Parameter(
     *     name="Authorization",
     *     in="header",
     *     type="string",
     *     required=true,
     *     description="The API token. e.g.: Bearer <TOKEN>"
     * )
     * @Swg\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     required=true,
     *     description="The ID of the User"
     * )
     *
     * @Swg\Response(
     *     response=200,
     *     description="Empty content"
     * )
     *
     * @Security("is_granted('CAN_DELETE_USER')")
     *
     * @return array
     */
    public function deleteAction(Request $request, $id)
    {
        $user = $this->getResource($id);
        $this->canAccessResourceOr403($user);

        $this->remove($user);

        return $this->view([], Response::HTTP_OK);
    }

    protected function getEntityClass()
    {
        return User::class;
    }

    public function canAccessResourceOr403($resource): void
    {
        if ($this->isGranted('CAN_MANAGE_ONLY_OWNED_USERS') && $this->getUser()->getStructure() !== null && !$this->getUser()->getStructure()->getUsers()->contains($resource)) {
            throw new AccessDeniedHttpException('Resource not found');
        }
    }
}
