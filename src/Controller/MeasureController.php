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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use PiaApi\Entity\Measure;

class MeasureController extends RestController
{
    /**
     * @FOSRest\Get("/measures")
     *
     * @return array
     */
    public function listAction(): View
    {
        $collection = $this->getRepository()->findAll();

        return $this->view($collection, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Get("/measures/example")
     *
     * @return array
     */
    public function exampleAction(Request $request)
    {
    }

    /**
     * @FOSRest\Get("/measures/{id}")
     *
     * @return array
     */
    public function showAction(Request $request, $id): View
    {
        $measure = $this->getRepository()->find($id);

        return $this->view($measure, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Post("/measures")
     *
     * @ParamConverter("measure", converter="fos_rest.request_body")
     *
     * @return array
     */
    public function createAction(Measure $measure)
    {
        $this->persist($measure);
        return $this->view($measure, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Put("/measures/{id}")
     * @FOSRest\Post("/measures/{id}")
     *
     * @ParamConverter("measure", converter="fos_rest.request_body")
     *
     * @return array
     */
    public function updateAction(Measure $measure)
    {

        $this->update($measure);
        return $this->view($measure, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Delete("/measures/{id}")
     * @ParamConverter("measure", converter="fos_rest.request_body")
     *
     * @return array
     */
    public function deleteAction(Measure $measure)
    {
      $this->remove($measure);
      return $this->view($measure, Response::HTTP_OK);
    }

    protected function getEntityClass()
    {
        return Measure::class;
    }
}
