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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use PiaApi\Exception\Folder\NonEmptyFolderCannotBeDeletedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use PiaApi\Entity\Pia\Folder;
use PiaApi\Services\FolderService;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use PiaApi\Exception\Folder\RootFolderCannotBeDeletedException;
use PiaApi\Entity\Pia\Structure;

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
     * @FOSRest\Get("/folders")
     * @Security("is_granted('CAN_SHOW_FOLDER')")
     *
     * @return View
     */
    public function listAction(Request $request)
    {
        $structure = $this->getUser()->getStructure();
        $collection = $this->getRepository()->findBy(['structure' => $structure, 'parent' => null]);

        return $this->view($collection, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Get("/folders/{id}")
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
     * @FOSRest\Post("/folders")
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
     * @FOSRest\Put("/folders/{id}", requirements={"id"="\d+"})
     * @FOSRest\Post("/folders/{id}", requirements={"id"="\d+"})
     * @Security("is_granted('CAN_EDIT_FOLDER')")
     *
     * @return View
     */
    public function updateAction(Request $request, $id)
    {
        $folder = $this->getResource($id);
        $this->canAccessResourceOr403($folder);

        $updatableAttributes = [
            'name'   => 'string',
            'parent' => Folder::class,
        ];

        $this->mergeFromRequest($folder, $updatableAttributes, $request);

        $this->update($folder);

        return $this->view($folder, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Delete("/folders/{id}", requirements={"id"="\d+"})
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
