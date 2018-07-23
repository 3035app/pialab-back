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

class StructureFolderController extends RestController
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
     * Lists all root Folders of a specific Structure.
     *
     * @Swg\Tag(name="StructureFolder")
     *
     * @FOSRest\Get("/structures/{structureId}/folders", requirements={"structureId"="\d+"})
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns all root Folders of given Structure",
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
    public function listAction(Request $request, $structureId)
    {
        $collection = $this->getRepository()->findBy(['structure' => $structureId, 'parent' => null], ['name' => 'ASC']);

        return $this->view($collection, Response::HTTP_OK);
    }

    /**
     * Shows one Folder by its ID and specific Structure.
     *
     * @Swg\Tag(name="StructureFolder")
     *
     * @FOSRest\Get("/structures/{structureId}/folders/{id}", requirements={"structureId"="\d+","id"="\d+"})
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
    public function showAction(Request $request, $structureId, $id)
    {
        $folder = $this->getRepository()->findOneBy(['structure' => $structureId, 'id' => $id]);
        if ($folder === null) {
            return $this->view($folder, Response::HTTP_NOT_FOUND);
        }
        $this->canAccessResourceOr403($folder);

        return $this->view($folder, Response::HTTP_OK);
    }

    /**
     * Creates a Folder for a specific Structure.
     *
     * @Swg\Tag(name="StructureFolder")
     *
     * @FOSRest\Post("/structures/{structureId}/folders",requirements={"structureId"="\d+"})
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
    public function createAction(Request $request, $structureId)
    {
        $parent = $request->get('parent') !== null
            ? $this->getResource($request->get('parent')['id'], Folder::class)
            : null;

        $structure = $this->getResource($structureId, Structure::class);

        $folder = $this->folderService->createFolder(
            $request->get('name'),
            $structure,
            $parent
        );
        $this->canAccessResourceOr403($folder);

        $this->persist($folder);

        $this->getRepository()->verify();
        $this->getRepository()->recover();

        $this->getDoctrine()->getManager()->flush();

        return $this->view($folder, Response::HTTP_OK);
    }

    /**
     * Updates a Folder of a specific Structure.
     *
     * @Swg\Tag(name="StructureFolder")
     *
     * @FOSRest\Put("/structures/{structureId}/folders/{id}", requirements={"structureId"="\d+","id"="\d+"})
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
    public function updateAction(Request $request, $structureId, $id)
    {
        $folder = $this->getRepository()->findOneBy(['structure' => $structureId, 'id' => $id]);
        if ($folder === null) {
            return $this->view($folder, Response::HTTP_NOT_FOUND);
        }

        $this->canAccessResourceOr403($folder);

        $updatableAttributes = [
            'name'   => RequestDataHandler::TYPE_STRING,
            'parent' => Folder::class,
        ];

        $this->mergeFromRequest($folder, $updatableAttributes, $request);

        $this->getRepository()->verify();
        $this->getRepository()->recover();

        $this->update($folder);

        return $this->view($folder, Response::HTTP_OK);
    }

    /**
     * Deletes a Folder of a specific Structure.
     *
     * @Swg\Tag(name="StructureFolder")
     *
     * @FOSRest\Delete("/structures/{structureId}/folders/{id}", requirements={"structureId"="\d+","id"="\d+"})
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
    public function deleteAction(Request $request, $structureId, $id)
    {
        $folder = $this->getRepository()->findOneBy(['structure' => $structureId, 'id' => $id]);
        $this->canAccessResourceOr403($folder);

        if (count($folder->getPias())) {
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
        $resourceStructure = $resource->getStructure();
        $structures = array_merge(
            [$this->getUser()->getStructure()],
            $this->getUser()->getProfile()->getPortfolioStructures());

        if ($resourceStructure === null || !in_array($resourceStructure, $structures)) {
            throw new AccessDeniedHttpException();
        }
    }
}
