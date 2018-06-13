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
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use PiaApi\Entity\Pia\Folder;
use PiaApi\Services\FolderService;

class FolderController extends RestController
{
    /**
     * @var FolderService
     */
    private $folderService;

    public function __construct(FolderService $folderService)
    {
        $this->folderService = $folderService;
    }

    /**
     * @FOSRest\Get("/folders")
     * @Security("is_granted('ROLE_PIA_LIST')")
     *
     * @return View
     */
    public function listAction(Request $request)
    {
        $this->canAccessRouteOr403();

        $structure = $this->getUser()->getStructure();
        $collection = $this->getRepository()->findBy(['structure' => $structure, 'parent' => null]);

        return $this->view($collection, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Get("/folders/{id}")
     * @Security("is_granted('ROLE_PIA_VIEW')")
     *
     * @return View
     */
    public function showAction(Request $request, $id)
    {
        $this->canAccessRouteOr403();

        $folder = $this->getResource($id);
        if ($folder === null) {
            return $this->view($folder, Response::HTTP_NOT_FOUND);
        }

        $this->canAccessResourceOr403($folder);

        return $this->view($folder, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Post("/folders")
     * @Security("is_granted('ROLE_PIA_CREATE')")
     *
     * @return View
     */
    public function createAction(Request $request)
    {
        $this->canAccessRouteOr403();

        if ($request->get('parent_id') === null && $request->get('parent') === null) {
            return $this->view('Missing parent identification', Response::HTTP_BAD_REQUEST);
        }

        $parent = $this->getResource($request->get('parent')['id'], Folder::class);

        $folder = $this->folderService->createFolderForStructureAndParent(
            $request->get('name'),
            $this->getUser()->getStructure(),
            $parent
        );

        $this->persist($folder);

        return $this->view($folder, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Put("/folders/{id}", requirements={"id"="\d+"})
     * @FOSRest\Post("/folders/{id}", requirements={"id"="\d+"})
     * @Security("is_granted('ROLE_PIA_EDIT')")
     *
     * @return View
     */
    public function updateAction(Request $request, $id)
    {
        $this->canAccessRouteOr403();

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
     * @Security("is_granted('ROLE_PIA_DELETE')")
     *
     * @return View
     */
    public function deleteAction(Request $request, $id)
    {
        $this->canAccessRouteOr403();

        $folder = $this->getResource($id);
        $this->canAccessResourceOr403($folder);
        $this->remove($folder);

        return $this->view($folder, Response::HTTP_OK);
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

        if ($resource->getStructure() !== $this->getUser()->getStructure()) {
            throw new AccessDeniedHttpException();
        }
    }
}
