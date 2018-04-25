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
use PiaApi\Entity\Comment;

class CommentController extends RestController
{
    /**
     * @FOSRest\Get("/comments")
     *
     * @return array
     */
    public function listAction(): View
    {
        $collection = $this->getRepository()->findAll();

        return $this->view($collection, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Get("/comments/example")
     *
     * @return array
     */
    public function exampleAction(Request $request)
    {
    }

    /**
     * @FOSRest\Get("/comments/{id}")
     *
     * @return array
     */
    public function showAction(Request $request, $id): View
    {
        $comment = $this->getRepository()->find($id);

        return $this->view($comment, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Post("/comments")
     *
     * @ParamConverter("comment", converter="fos_rest.request_body")
     *
     * @return array
     */
    public function createAction(Comment $comment)
    {
        $this->persist($comment);
        return $this->view($comment, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Put("/comments/{id}")
     * @FOSRest\Post("/comments/{id}")
     *
     * @ParamConverter("comment", converter="fos_rest.request_body")
     *
     * @return array
     */
    public function updateAction(Comment $comment)
    {

        $this->update($comment);
        return $this->view($comment, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Delete("/comments/{id}")
     * @ParamConverter("comment", converter="fos_rest.request_body")
     *
     * @return array
     */
    public function deleteAction(Comment $comment)
    {
      $this->remove($comment);
      return $this->view($comment, Response::HTTP_OK);
    }

    protected function getEntityClass()
    {
        return Attachment::class;
    }

}
