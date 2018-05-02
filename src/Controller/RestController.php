<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Util\Inflector as Inflector;
use PiaApi\Entity\Pia;

abstract class RestController extends FOSRestController
{
    protected function persist($entity)
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    protected function update($entity)
    {
        $this->getEntityManager()->merge($entity);
        $this->getEntityManager()->flush();
    }

    protected function remove($entity)
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    protected function getEntityManager()
    {
        return $this->getDoctrine()->getManager();
    }

    protected function getRepository()
    {
        return $this->getDoctrine()->getRepository($this->getEntityClass());
    }

    protected function extractData(Request $request, $key = null)
    {
        $data = $request->request->all();
        //if ($key !== null) {
        //    $data = $data[$key];
        //}

        return array_filter($data, function ($item) {
            return $item != 'undefined';
        });
    }

    protected function extractCriteria(Request $request, $default = [])
    {
        $query = $request->query->all();
        if (empty($query)) {
            return [];
        }
        $criteria = array_merge(...array_map(function ($key, $value) {
            return [Inflector::camelize($key) => $value];
        }, array_keys($query), $query));

        return array_merge($criteria, $default);
    }

    protected function extractPiaId(Request $request, $key = null)
    {
        $data = $request->request->all();
        if ($key !== null) {
            $data = $data[$key];
        }

        return $data['pia_id'] ?? $request->get('pia_id');
    }

    protected function newFromArray($data, $piaId = null)
    {
        $entity = $this->get('jms_serializer')->fromArray($data, $this->getEntityClass());
        if ($piaId !== null) {
            $entity->setPia($this->getEntityManager()->getReference(Pia::class, $piaId));
        }

        return $entity;
    }

    protected function newFromRequest(Request $request, $piaId = null)
    {
        $entity = $this->get('jms_serializer')->deserialize($request->getContent(), $this->getEntityClass(),'json');
        if ($piaId !== null) {
            $entity->setPia($this->getEntityManager()->getReference(Pia::class, $piaId));
        }

        return $entity;
    }

    abstract protected function getEntityClass();
}
