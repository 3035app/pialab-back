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
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use PiaApi\Entity\Pia\Pia;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class PiaController extends RestController
{
    /**
     * @FOSRest\Get("/pias/example")
     *
     * @return array
     */
    public function exampleAction(Request $request)
    {
        $this->canAccessRouteOr304();
    }

    /**
     * @FOSRest\Get("/pias")
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
