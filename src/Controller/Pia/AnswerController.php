<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Controller\Pia;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as Swg;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use PiaApi\Entity\Pia\Answer;

class AnswerController extends PiaSubController
{
    /**
     * @Swg\Tag(name="Answer")
     *
     * @FOSRest\Get("/pias/{piaId}/answers")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns all Answsers for a specific Treatment",
     *     @Swg\Schema(
     *         type="array",
     *         @Swg\Items(ref=@Nelmio\Model(type=Answer::class, groups={"Default"}))
     *     )
     * )
     *
     * @Security("is_granted('CAN_SHOW_ANSWER')")
     */
    public function listAction(Request $request, $piaId)
    {
        return parent::listAction($request, $piaId);
    }

    /**
     * @Swg\Tag(name="Answer")
     *
     * @FOSRest\Get("/pias/{piaId}/answers/{id}")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns one Answer by its id and for a specific Treatment",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Answer::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_SHOW_ANSWER")
     */
    public function showAction(Request $request, $piaId, $id)
    {
        return parent::showAction($request, $piaId, $id);
    }

    /**
     * @Swg\Tag(name="Answer")
     *
     * @FOSRest\Post("/pias/{piaId}/answers")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Creates an Answer for a specific Treatment",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Answer::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_CREATE_ANSWER')")
     */
    public function createAction(Request $request, $piaId)
    {
        return parent::createAction($request, $piaId);
    }

    /**
     * @Swg\Tag(name="Answer")
     *
     * @FOSRest\Put("/pias/{piaId}/answers/{id}")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Update an Answer for a specific Treatment",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Answer::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_EDIT_ANSWER')")
     */
    public function updateAction(Request $request, $piaId, $id)
    {
        return parent::updateAction($request, $piaId, $id);
    }

    /**
     * @Swg\Tag(name="Answer")
     *
     * @FOSRest\Delete("pias/{piaId}/answers/{id}")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Delete an Answer for a specific Treatment",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Answer::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_DELETE_ANSWER')")
     *
     * @return array
     */
    public function deleteAction(Request $request, $piaId, $id)
    {
        return parent::deleteAction($request, $piaId, $id);
    }

    protected function getEntityClass()
    {
        return Answer::class;
    }
}
