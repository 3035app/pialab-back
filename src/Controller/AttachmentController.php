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
use PiaApi\Entity\Attachment;

class AttachmentController extends RestController
{
    /**
     * @FOSRest\Get("/attachments")
     *
     * @return array
     */
    public function listAction(): View
    {
        $collection = $this->getRepository()->findAll();

        return $this->view($collection, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Get("/attachments/example")
     *
     * @return array
     */
    public function exampleAction(Request $request)
    {
    }

    /**
     * @FOSRest\Get("/attachments/{id}")
     *
     * @return array
     */
    public function showAction(Request $request, $id): View
    {
        $attachment = $this->getRepository()->find($id);

        return $this->view($attachment, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Post("/attachments")
     *
     * @ParamConverter("attachment", converter="fos_rest.request_body")
     *
     * @return array
     */
    public function createAction(Attachment $attachment)
    {
        $this->persist($attachment);
        return $this->view($attachment, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Put("/attachments/{id}")
     * @FOSRest\Post("/attachments/{id}")
     *
     * @ParamConverter("attachment", converter="fos_rest.request_body")
     *
     * @return array
     */
    public function updateAction(Attachment $attachment)
    {

        $this->update($attachment);
        return $this->view($attachment, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Delete("/attachments/{id}")
     * @ParamConverter("attachment", converter="fos_rest.request_body")
     *
     * @return array
     */
    public function deleteAction(Attachment $attachment)
    {
      $this->remove($attachment);
      return $this->view($attachment, Response::HTTP_OK);
    }

    protected function getEntityClass()
    {
        return Attachment::class;
    }

}
