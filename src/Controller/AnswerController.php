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
use PiaApi\Entity\Answer;
use PiaApi\Entity\Pia;

class AnswerController extends RestController
{
    /**
     * @FOSRest\Get("/pias/{piaId}/answers")
     *
     * @return array
     */
    public function listAction(Request $request, $piaId)
    {
        $criteria = $this->extractCriteria($request, ['pia' => $piaId]);
        $collection = $this->getRepository()->findBy($criteria);

        return $this->view($collection, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Get("/pias/{piaId}/answers/{id}")
     *
     * @return array
     */
    public function showAction(Request $request, $piaId = null, $id): View
    {
        $answer = $this->getRepository()->find($id);

        return $this->view($answer, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Post("/pias/{piaId}/answers")
     */
    public function createAction(Request $request, $piaId)
    {
        $answerData = $this->extractData($request);
        $answer = $this->newFromArray($answerData, $piaId);
        $this->persist($answer);

        return $this->view($answer, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Put("/pias/{piaId}/answers/{id}")
     * @FOSRest\Patch("/pias/{piaId}/answers/{id}")
     * @FOSRest\Post("/pias/{piaId}/answers/{id}")
     *
     * @return array
     */
    public function updateAction(Request $request, $piaId, $id)
    {
        $answerData = $this->extractData($request);
        $answer = $this->newFromArray($answerData, $piaId);
        $this->update($answer);

        return $this->view($answer, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Delete("pias/{piaId}/answers/{id}")
     * @ParamConverter("answer", converter="fos_rest.request_body")
     *
     * @return array
     */
    public function deleteAction(Request $request, $piaId, $id)
    {
        $answer = $this->getRepository()->find($id);
        $this->remove($answer);

        return $this->view($answer, Response::HTTP_OK);
    }

    protected function getEntityClass()
    {
        return Answer::class;
    }
}
