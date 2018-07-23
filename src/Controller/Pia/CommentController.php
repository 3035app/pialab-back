<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Controller\Pia;

use FOS\RestBundle\Controller\Annotations as FOSRest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use PiaApi\Entity\Pia\Comment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as Swg;
use Symfony\Component\HttpFoundation\Request;

class CommentController extends PiaSubController
{
    /**
     * @Swg\Tag(name="Comment")
     *
     * @FOSRest\Get("/pias/{piaId}/comments")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns all comments for a specific Treatment",
     *     @Swg\Schema(
     *         type="array",
     *         @Swg\Items(ref=@Nelmio\Model(type=Comment::class, groups={"Default"}))
     *     )
     * )
     *
     * @Security("is_granted('CAN_SHOW_COMMENT')")
     */
    public function listAction(Request $request, $piaId)
    {
        return parent::listAction($request, $piaId);
    }

    /**
     * @Swg\Tag(name="Comment")
     *
     * @FOSRest\Get("/pias/{piaId}/comments/{id}")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns one Comment by its id and for a specific Treatment",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Comment::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_SHOW_COMMENT')")
     */
    public function showAction(Request $request, $piaId, $id)
    {
        return parent::showAction($request, $piaId, $id);
    }

    /**
     * @Swg\Tag(name="Comment")
     *
     * @FOSRest\Post("/pias/{piaId}/comments")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Creates an Comment for a specific Treatment",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Comment::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_CREATE_COMMENT')")
     */
    public function createAction(Request $request, $piaId)
    {
        return parent::createAction($request, $piaId);
    }

    /**
     * @Swg\Tag(name="Comment")
     *
     * @FOSRest\Put("/pias/{piaId}/comments/{id}")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Update an Comment for a specific Treatment",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Comment::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_EDIT_COMMENT')")
     */
    public function updateAction(Request $request, $piaId, $id)
    {
        return parent::updateAction($request, $piaId, $id);
    }

    /**
     * @Swg\Tag(name="Comment")
     *
     * @FOSRest\Delete("pias/{piaId}/comments/{id}")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Delete an Comment for a specific Treatment",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Comment::class, groups={"Default"})
     *     )
     * )
     *
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
