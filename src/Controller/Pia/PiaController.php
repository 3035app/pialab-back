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
use PiaApi\Entity\Pia\Pia;
use PiaApi\DataExchange\Transformer\JsonToEntityTransformer;

class PiaController extends RestController
{
    /**
     * @var jsonToEntityTransformer
     */
    protected $jsonToEntityTransformer;

    public function __construct(JsonToEntityTransformer $jsonToEntityTransformer)
    {
        $this->jsonToEntityTransformer = $jsonToEntityTransformer;
    }

    /**
     * @FOSRest\Get("/pias")
     * @Security("is_granted('ROLE_PIA_LIST')")
     *
     * @return array
     */
    public function listAction(Request $request)
    {
        $this->canAccessRouteOr304();

        $structure = $this->getUser()->getStructure();

        $criteria = array_merge($this->extractCriteria($request), ['structure' => $structure]);
        $collection = $this->getRepository()->findBy($criteria);

        return $this->view($collection, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Get("/pias/{id}")
     * @Security("is_granted('ROLE_PIA_VIEW')")
     *
     * @return array
     */
    public function showAction(Request $request, $id)
    {
        $this->canAccessRouteOr304();

        $pia = $this->getRepository()->find($id);
        if ($pia === null) {
            return $this->view($pia, Response::HTTP_NOT_FOUND);
        }

        $this->canAccessResourceOr304($pia);

        return $this->view($pia, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Post("/pias")
     * @Security("is_granted('ROLE_PIA_CREATE')")
     *
     * @return array
     */
    public function createAction(Request $request)
    {
        $this->canAccessRouteOr304();

        $pia = $this->newFromRequest($request);
        $pia->setStructure($this->getUser()->getStructure());
        $this->persist($pia);

        return $this->view($pia, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Put("/pias/{id}")
     * @FOSRest\Post("/pias/{id}")
     * @Security("is_granted('ROLE_PIA_EDIT')")
     *
     * @return array
     */
    public function updateAction(Request $request, $id)
    {
        $this->canAccessRouteOr304();

        $pia = $this->newFromRequest($request);
        $this->canAccessResourceOr304($pia);
        $this->update($pia);

        return $this->view($pia, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Delete("/pias/{id}")
     * @Security("is_granted('ROLE_PIA_DELETE')")
     *
     * @return array
     */
    public function deleteAction(Request $request, $id)
    {
        $this->canAccessRouteOr304();

        $pia = $this->getRepository()->find($id);
        $this->canAccessResourceOr304($pia);
        $this->remove($pia);

        return $this->view($pia, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Post("/pias/import")
     * @Security("is_granted('ROLE_PIA_CREATE')")
     *
     * @return array
     */
    public function importAction(Request $request)
    {
        $this->canAccessRouteOr304();

        $importData = $request->get('data', null);
        if ($importData === null) {
            return $this->view($importData, Response::HTTP_BAD_REQUEST);
        }

        $pia = $this->jsonToEntityTransformer->transform($importData);
        $this->persist($pia);

        return $this->view($pia, Response::HTTP_OK);
    }

    protected function getEntityClass()
    {
        return Pia::class;
    }

    public function canAccessResourceOr304($resource): void
    {
        if (!$resource instanceof Pia) {
            throw new AccessDeniedHttpException();
        }

        if ($resource->getStructure() !== $this->getUser()->getStructure()) {
            throw new AccessDeniedHttpException();
        }
    }
}
