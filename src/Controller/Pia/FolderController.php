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
     * @Swg\Tag(name="Folder")
     *
     * @FOSRest\Get("/folders")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns all Answsers",
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
        $collection = $this->getRepository()->findBy(['structure' => $structureId, 'parent' => null]);

        return $this->view($collection, Response::HTTP_OK);
    }

    /**
     * @Swg\Tag(name="Folder")
     *
     * @FOSRest\Get("/folders/{id}")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns one Folder by its id",
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
     * @Swg\Tag(name="Folder")
     *
     * @FOSRest\Post("/folders")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Creates a Folder",
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

        return $this->view($folder, Response::HTTP_OK);
    }

    /**
     * @Swg\Tag(name="Folder")
     *
     * @FOSRest\Put("/folders/{id}", requirements={"id"="\d+"})
     *
     * @Swg\Response(
     *     response=200,
     *     description="Update a Folder",
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

        return $this->view($folder, Response::HTTP_OK);
    }

    /**
     * @Swg\Tag(name="Folder")
     *
     * @FOSRest\Delete("/folders/{id}", requirements={"id"="\d+"})
     *
     * @Swg\Response(
     *     response=200,
     *     description="Delete a Folder",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Folder::class, groups={"Default"})
     *     )
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

        if (count($folder->getPias())) {
            throw new NonEmptyFolderCannotBeDeletedException();
        }

        if ($folder->isRoot() && $folder->getStructure() !== null) {
            throw new RootFolderCannotBeDeletedException();
        }

        $this->remove($folder);

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
