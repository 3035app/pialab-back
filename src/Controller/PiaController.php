<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenserializeDateTimeToJsonced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use PiaApi\Entity\Pia;


class PiaController extends RestController
{
    /**
     * @FOSRest\Get("/pias")
     *
     * @return array
     */
    public function listAction()
    {
        $collection = $this->getRepository()->findAll();
        return $this->view($collection, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Get("/pias/example")
     *
     * @return array
     */
    public function exampleAction(Request $request)
    {
    }



    /**
     * @FOSRest\Get("/pias/{id}")
     *
     * @return array
     */
    public function showAction(Request $request, $id)
    {

        $pia = $this->getRepository()->find($id);
        return $this->view($pia, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Post("/pias")
     *
     * @return array
     */
    public function createAction(Request $request)
    {
        $data = $request->request->all();
        $piaData = array_filter($data['pia'], function($item){
          return $item != 'undefined';
        });

        $pia = $this->get('jms_serializer')->fromArray($piaData, $this->getEntityClass());

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
        $pia = $this->getRepository()->find($id);
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
      $pia = $this->getRepository()->find($id);
      $this->remove($pia);
      return $this->view($pia, Response::HTTP_OK);
    }

    protected function getEntityClass()
    {
        return Pia::class;
    }

}
