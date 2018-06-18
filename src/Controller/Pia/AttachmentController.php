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
use PiaApi\Entity\Pia\Attachment;

class AttachmentController extends PiaSubController
{
    /**
     * @FOSRest\Get("/pias/{piaId}/attachments")
     * @Security("is_granted('CAN_SHOW_PIA')")
     */
    public function listAction(Request $request, $piaId)
    {
        return parent::listAction($request, $piaId);
    }

    /**
     * @FOSRest\Get("/pias/{piaId}/attachments/{id}")
     * @Security("is_granted('CAN_SHOW_PIA')")
     */
    public function showAction(Request $request, $piaId, $id)
    {
        return parent::showAction($request, $piaId, $id);
    }

    /**
     * @FOSRest\Post("/pias/{piaId}/attachments")
     * @Security("is_granted('CAN_EDIT_PIA')")
     */
    public function createAction(Request $request, $piaId)
    {
        return parent::createAction($request, $piaId);
    }

    /**
     * @FOSRest\Put("/pias/{piaId}/attachments/{id}")
     * @FOSRest\Patch("/pias/{piaId}/attachments/{id}")
     * @FOSRest\Post("/pias/{piaId}/attachments/{id}")
     * @Security("is_granted('CAN_EDIT_PIA')")
     */
    public function updateAction(Request $request, $piaId, $id)
    {
        return parent::updateAction($request, $piaId, $id);
    }

    /**
     * @FOSRest\Delete("pias/{piaId}/attachments/{id}")
     * @Security("is_granted('CAN_EDIT_PIA')")
     *
     * @return array
     */
    public function deleteAction(Request $request, $piaId, $id)
    {
        return parent::deleteAction($request, $piaId, $id);
    }

    protected function getEntityClass()
    {
        return Attachment::class;
    }
}
