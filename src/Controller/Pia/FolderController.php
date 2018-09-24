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
use PiaApi\DataHandler\RequestDataHandler;
use PiaApi\Entity\Pia\Folder;
use PiaApi\Entity\Pia\Structure;
use PiaApi\Exception\Folder\NonEmptyFolderCannotBeDeletedException;
use PiaApi\Exception\Folder\RootFolderCannotBeDeletedException;
use PiaApi\Services\FolderService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as Swg;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class FolderController extends RestController
{
    /**
     * @var FolderService
     */
    private $folderService;

    public function __construct(
        PropertyAccessorInterface $propertyAccessor,
        FolderService $folderService
    ) {
        parent::__construct($propertyAccessor);
        $this->folderService = $folderService;
    }

    /**
     * Lists all Folders of User's structure.
     *
     * @Swg\Tag(name="Folder")
     *
     * @FOSRest\Get("/folders")
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
     *     description="Returns all Folders",
     *     @Swg\Schema(
     *         type="array",
     *         @Swg\Items(ref=@Nelmio\Model(type=Folder::class, groups={"Default"}))
     *     )
     * )
     *
     * @Security("is_granted('CAN_SHOW_FOLDER')")
     *
     * @return View
     */
    public function listAction(Request $request)
    {
        $structureId = $this->getUser()->getStructure() !== null ? $this->getUser()->getStructure()->getId() : null;
        $collection = $this->getRepository()->findBy(['structure' => $structureId, 'parent' => null], ['name' => 'ASC']);

        return $this->view($collection, Response::HTTP_OK);
    }

    /**
     * Shows one Folder by its ID.
     *
     * @Swg\Tag(name="Folder")
     *
     * @FOSRest\Get("/folders/{id}")
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
     *     description="The ID of the Folder"
     * )
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns one Folder",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Folder::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_SHOW_FOLDER')")
     *
     * @return View
     */
    public function showAction(Request $request, $id)
    {
        $folder = $this->getResource($id);
        if ($folder === null) {
            return $this->view($folder, Response::HTTP_NOT_FOUND);
        }

        $this->canAccessResourceOr403($folder);

        return $this->view($folder, Response::HTTP_OK);
    }

    /**
     * Creates a Folder.
     *
     * @Swg\Tag(name="Folder")
     *
     * @FOSRest\Post("/folders")
     *
     * @Swg\Parameter(
     *     name="Authorization",
     *     in="header",
     *     type="string",
     *     required=true,
     *     description="The API token. e.g.: Bearer <TOKEN>"
     * )
     * @Swg\Parameter(
     *     name="Folder",
     *     in="body",
     *     required=true,
     *     @Swg\Schema(
     *         type="object",
     *         required={"name"},
     *         @Swg\Property(property="name", type="string"),
     *         @Swg\Property(property="parent", type="object", @Swg\Property(property="id", type="number"))
     *     ),
     *     description="The Folder content"
     * )
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns the newly created Folder",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Folder::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_CREATE_FOLDER')")
     *
     * @return View
     */
    public function createAction(Request $request)
    {
        $parent = $request->get('parent') !== null ? $this->getResource($request->get('parent')['id'], Folder::class) : null;

        $structure = $this->getUser()->getStructure();

        $folder = $this->folderService->createFolder(
            $request->get('name'),
            $structure,
            $parent
        );

        $this->persist($folder);

        $this->getRepository()->verify();
        $this->getRepository()->recover();

        $this->getDoctrine()->getManager()->flush();

        return $this->view($folder, Response::HTTP_OK);
    }

    /**
     * Updates a Folder.
     *
     * @Swg\Tag(name="Folder")
     *
     * @FOSRest\Put("/folders/{id}", requirements={"id"="\d+"})
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
     *     description="The ID of the Folder"
     * )
     * @Swg\Parameter(
     *     name="Folder",
     *     in="body",
     *     required=true,
     *     @Swg\Schema(
     *         type="object",
     *         @Swg\Property(property="name", type="string"),
     *         @Swg\Property(property="parent", type="object", @Swg\Property(property="id", type="number"))
     *     ),
     *     description="The Folder content"
     * )
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns the updated Folder",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Folder::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_EDIT_FOLDER')")
     *
     * @return View
     */
    public function updateAction(Request $request, $id)
    {
        $folder = $this->getResource($id);
        $this->canAccessResourceOr403($folder);

        $updatableAttributes = [
            'name'   => RequestDataHandler::TYPE_STRING,
            'parent' => Folder::class,
        ];

        $this->mergeFromRequest($folder, $updatableAttributes, $request);

        $this->update($folder);

        $this->getRepository()->verify();
        $this->getRepository()->recover();

        $this->getDoctrine()->getManager()->flush();

        return $this->view($folder, Response::HTTP_OK);
    }

    /**
     * Deletes a Folder.
     *
     * @Swg\Tag(name="Folder")
     *
     * @FOSRest\Delete("/folders/{id}", requirements={"id"="\d+"})
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
     *     description="The ID of the Folder"
     * )
     *
     * @Swg\Response(
     *     response=200,
     *     description="Empty content"
     * )
     *
     * @Security("is_granted('CAN_DELETE_FOLDER')")
     *
     * @return View
     */
    public function deleteAction(Request $request, $id)
    {
        $folder = $this->getResource($id);
        $this->canAccessResourceOr403($folder);

        if (count($folder->getProcessings())) {
            throw new NonEmptyFolderCannotBeDeletedException();
        }

        if ($folder->isRoot() && $folder->getStructure() !== null) {
            throw new RootFolderCannotBeDeletedException();
        }

        $this->remove($folder);

        $this->getRepository()->verify();
        $this->getRepository()->recover();

        $this->getDoctrine()->getManager()->flush();

        return $this->view([], Response::HTTP_OK);
    }

    protected function getEntityClass()
    {
        return Folder::class;
    }

    public function canAccessResourceOr403($resource): void
    {
        if (!$resource instanceof Folder) {
            throw new AccessDeniedHttpException();
        }

        if ($resource->getStructure() !== null && $resource->getStructure() !== $this->getUser()->getStructure()) {
            throw new AccessDeniedHttpException();
        }
    }
}
