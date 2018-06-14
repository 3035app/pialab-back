<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Controller\Pia;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as Swg;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use FOS\RestBundle\View\View;
use PiaApi\Entity\Pia\Structure;

class StructureController extends RestController
{
    /**
     * @Swg\Tag(name="Structure")
     *
     * @FOSRest\Get("/structures")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns all structures",
     *     @Swg\Schema(
     *         type="array",
     *         @Swg\Items(ref=@Nelmio\Model(type=Structure::class, groups={"Default"}))
     *     )
     * )
     * @Security("is_granted('CAN_SHOW_STRUCTURE')")
     *
     * @return array
     */
    public function listAction(Request $request)
    {
        return $this->view([], Response::HTTP_OK);
    }

    /**
     * @Swg\Tag(name="Structure")
     *
     * @FOSRest\Get("/structures/{id}", requirements={"id"="\d+"})
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns one Structure by its id",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Structure::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_SHOW_STRUCTURE')")
     *
     * @return array
     */
    public function showAction(Request $request, $id)
    {
        return $this->view([], Response::HTTP_OK);
    }

    /**
     * @Swg\Tag(name="Structure")
     *
     * @FOSRest\Post("/structures")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Creates a structure",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=STRUCTURE::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_CREATE_STRUCTURE')")
     *
     * @return array
     */
    public function createAction(Request $request)
    {
        return $this->view([], Response::HTTP_OK);
    }

    /**
     * @Swg\Tag(name="Structure")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Update a structure",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Structure::class, groups={"Default"})
     *     )
     * )
     *
     * @FOSRest\Put("/structures/{id}", requirements={"id"="\d+"})
     *
     * @Security("is_granted('CAN_EDIT_STRUCTURE')")
     *
     * @return array
     */
    public function updateAction(Request $request, $id)
    {
        return $this->view([], Response::HTTP_OK);
    }

    /**
     * @Swg\Tag(name="Structure")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Delete a structure",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Structure::class, groups={"Default"})
     *     )
     * )
     *
     * @FOSRest\Delete("/structures/{id}", requirements={"id"="\d+"})
     *
     * @Security("is_granted('CAN_DELETE_STRUCTURE')")
     *
     * @return array
     */
    public function deleteAction(Request $request, $id)
    {
        return $this->view([], Response::HTTP_OK);
    }

    protected function getEntityClass()
    {
        return Structure::class;
    }
}
