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
use PiaApi\Entity\Evaluation;

class EvaluationController extends RestController
{
    /**
     * @FOSRest\Get("/evaluations")
     *
     * @return array
     */
    public function listAction(): View
    {
        $collection = $this->getRepository()->findAll();

        return $this->view($collection, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Get("/evaluations/example")
     *
     * @return array
     */
    public function exampleAction(Request $request)
    {
    }

    /**
     * @FOSRest\Get("/evaluations/{id}")
     *
     * @return array
     */
    public function showAction(Request $request, $id): View
    {
        $evaluation = $this->getRepository()->find($id);

        return $this->view($evaluation, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Post("/evaluations")
     *
     * @ParamConverter("evaluation", converter="fos_rest.request_body")
     *
     * @return array
     */
    public function createAction(Evaluation $evaluation)
    {
        $this->persist($evaluation);
        return $this->view($evaluation, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Put("/evaluations/{id}")
     * @FOSRest\Post("/evaluations/{id}")
     *
     * @ParamConverter("evaluation", converter="fos_rest.request_body")
     *
     * @return array
     */
    public function updateAction(Evaluation $evaluation)
    {

        $this->update($evaluation);
        return $this->view($evaluation, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Delete("/evaluations/{id}")
     * @ParamConverter("evaluation", converter="fos_rest.request_body")
     *
     * @return array
     */
    public function deleteAction(Evaluation $evaluation)
    {
      $this->remove($evaluation);
      return $this->view($evaluation, Response::HTTP_OK);
    }

    protected function getEntityClass()
    {
        return Evaluation::class;
    }

}
