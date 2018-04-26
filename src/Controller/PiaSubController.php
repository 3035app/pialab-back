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
use PiaApi\Entity\Pia;

abstract class PiaSubController extends RestController
{
    protected static $DATA_KEY = null;

    public function listAction(Request $request, $piaId)
    {
        $criteria = $this->extractCriteria($request, ['pia' => $piaId]);
        $collection = $this->getRepository()->findBy($criteria);

        return $this->view($collection, Response::HTTP_OK);
    }

    public function showAction(Request $request, $piaId, $id)
    {
        $entity = $this->getRepository()->find($id);

        return $this->view($entity, Response::HTTP_OK);
    }

    public function createAction(Request $request, $piaId)
    {
        $entityData = $this->extractData($request, static::$DATA_KEY);
        
        $entity = $this->newFromArray($entityData, $piaId);
        $this->persist($entity);

        return $this->view($entity, Response::HTTP_OK);
    }

    public function updateAction(Request $request, $piaId, $id)
    {
        $entityData = $this->extractData($request, static::$DATA_KEY);
        $entity = $this->newFromArray($entityData, $piaId);

        $this->update($entity);

        return $this->view($entity, Response::HTTP_OK);
    }

    public function deleteAction(Request $request, $piaId, $id)
    {
        $entity = $this->getRepository()->find($id);
        $this->remove($entity);

        return $this->view($entity, Response::HTTP_OK);
    }
}
