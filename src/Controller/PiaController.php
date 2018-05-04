<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use PiaApi\Entity\Pia\Pia;

class PiaController extends RestController
{
    protected static $DATA_KEY = 'pia';

    /**
     * @FOSRest\Get("/pias/example")
     *
     * @return array
     */
    public function exampleAction(Request $request)
    {
        $this->canAccessResourceOr304();
    }

    /**
     * @FOSRest\Get("/pias")
     *
     * @return array
     */
    public function listAction(Request $request)
    {
        $this->canAccessResourceOr304();

        $criteria = $this->extractCriteria($request);
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
        $this->canAccessResourceOr304();

        $pia = $this->getRepository()->find($id);
        if ($pia === null) {
            return $this->view($pia, Response::HTTP_NOT_FOUND);
        }
        return $this->view($pia, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Post("/pias")
     *
     * @return array
     */
    public function createAction(Request $request)
    {
        $this->canAccessResourceOr304();

        $entity = $this->newFromRequest($request);
        $this->persist($entity);

        return $this->view($entity, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Put("/pias/{id}")
     * @FOSRest\Post("/pias/{id}")
     *
     * @return array
     */
    public function updateAction(Request $request, $id)
    {
        $this->canAccessResourceOr304();

        $pia = $this->getRepository()->find($id);

        $this->update($pia);

        return $this->view($entity, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Delete("/pias/{id}")
     *
     * @return array
     */
    public function deleteAction(Request $request, $id)
    {
        $this->canAccessResourceOr304();
        
        $pia = $this->getRepository()->find($id);
        $this->remove($pia);

        return $this->view($pia, Response::HTTP_OK);
    }

    protected function getEntityClass()
    {
        return Pia::class;
    }
}
