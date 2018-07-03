<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Controller\Pia;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use PiaApi\Entity\Pia\Comment;

class CommentController extends PiaSubController
{
    /**
     * @FOSRest\Get("/pias/{piaId}/comments")
     * @Security("is_granted('CAN_SHOW_COMMENT')")
     */
    public function listAction(Request $request, $piaId)
    {
        return parent::listAction($request, $piaId);
    }

    /**
     * @FOSRest\Get("/pias/{piaId}/comments/{id}")
     * @Security("is_granted('CAN_SHOW_COMMENT')")
     */
    public function showAction(Request $request, $piaId, $id)
    {
        return parent::showAction($request, $piaId, $id);
    }

    /**
     * @FOSRest\Post("/pias/{piaId}/comments")
     * @Security("is_granted('CAN_CREATE_COMMENT')")
     */
    public function createAction(Request $request, $piaId)
    {
        return parent::createAction($request, $piaId);
    }

    /**
     * @FOSRest\Put("/pias/{piaId}/comments/{id}")
     * @FOSRest\Patch("/pias/{piaId}/comments/{id}")
     * @FOSRest\Post("/pias/{piaId}/comments/{id}")
     * @Security("is_granted('CAN_EDIT_COMMENT')")
     */
    public function updateAction(Request $request, $piaId, $id)
    {
        return parent::updateAction($request, $piaId, $id);
    }

    /**
     * @FOSRest\Delete("pias/{piaId}/comments/{id}")
     * @Security("is_granted('CAN_DELETE_COMMENT')")
     *
     * @return array
     */
    public function deleteAction(Request $request, $piaId, $id)
    {
        return parent::deleteAction($request, $piaId, $id);
    }

    protected function getEntityClass()
    {
        return Comment::class;
    }
}
