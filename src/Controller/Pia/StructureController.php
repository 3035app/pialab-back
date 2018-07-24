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
use PiaApi\Entity\Pia\Structure;
use PiaApi\Services\StructureService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as Swg;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use PiaApi\Entity\Pia\StructureType;
use PiaApi\Entity\Pia\Portfolio;
use PiaApi\DataHandler\RequestDataHandler;

class StructureController extends RestController
{
    /**
     * @var StructureService
     */
    private $structureService;

    public function __construct(
        PropertyAccessorInterface $propertyAccessor,
        StructureService $structureService
    ) {
        parent::__construct($propertyAccessor);
        $this->structureService = $structureService;
    }

    /**
     * Lists all Structures.
     *
     * @Swg\Tag(name="Structure")
     *
     * @FOSRest\Get("/structures")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns all Structures",
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
        $criteria = array_merge($this->extractCriteria($request), ['limit' => 20, 'page' => 1]);

        /* @var Pagerfanta $structuresPager */
        $structuresPager;
        $portfolios = null;

        if ($this->isGranted('CAN_MANAGE_ONLY_OWNED_STRUCTURES')) {
            $portfolios = $this->getUser()->getPortfolios();
        }

        if (count($portfolios) > 0) {
            $structuresPager = $this->getDoctrine()
                ->getRepository($this->getEntityClass())
                ->getPaginatedStructuresForPortfolios($portfolios, $criteria['limit'], $criteria['page']);
        } else {
            $structuresPager = $this->getDoctrine()
                ->getRepository($this->getEntityClass())
                ->getPaginatedStructures($criteria['limit'], $criteria['page']);
        }

        return $this->view($structuresPager->getCurrentPageResults()->getArrayCopy(), Response::HTTP_OK);
    }

    /**
     * Shows one Structure by its ID.
     *
     * @Swg\Tag(name="Structure")
     *
     * @FOSRest\Get("/structures/{id}", requirements={"id"="\d+"})
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns one Structure",
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
        $entity = $this->getRepository()->find($id);

        if ($entity === null) {
            return $this->view($entity, Response::HTTP_NOT_FOUND);
        }

        $this->canAccessResourceOr403($entity);

        return $this->view($entity, Response::HTTP_OK);
    }

    /**
     * Creates a Structure.
     *
     * @Swg\Tag(name="Structure")
     *
     * @FOSRest\Post("/structures")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns the newly created Structure",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Structure::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_CREATE_STRUCTURE')")
     *
     * @return array
     */
    public function createAction(Request $request)
    {
        $structureType = $this->getRepository(StructureType::class)->find($request->get('structureType', -1));
        $portfolio = $this->getRepository(Portfolio::class)->find($request->get('portfolio', -1));

        $structure = $this->structureService->createStructure(
            $request->get('name'),
            $structureType,
            $portfolio
        );

        $this->persist($structure);

        return $this->view($structure, Response::HTTP_OK);
    }

    /**
     * Updates a structure.
     *
     * @Swg\Tag(name="Structure")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns the updated Structure",
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
        $structure = $this->getResource($id);
        $this->canAccessResourceOr403($structure);

        $updatableAttributes = [
            'name'      => RequestDataHandler::TYPE_STRING,
            'type'      => StructureType::class,
            'portfolio' => Portfolio::class,
        ];

        $this->mergeFromRequest($structure, $updatableAttributes, $request);

        $this->update($structure);

        return $this->view($structure, Response::HTTP_OK);
    }

    /**
     * Deletes a structure.
     *
     * @Swg\Tag(name="Structure")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Empty content"
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
        $structure = $this->getResource($id);

        $this->remove($structure);

        return $this->view([], Response::HTTP_OK);
    }

    protected function getEntityClass()
    {
        return Structure::class;
    }

    public function canAccessResourceOr403($resource): void
    {
        if ($this->isGranted('CAN_MANAGE_ONLY_OWNED_STRUCTURES') && count($this->getUser()->getPortfolios()) > 0 && in_array($resource, $this->getUser()->getPortfolioStructures())) {
            throw new AccessDeniedHttpException('Resource not found');
        }
    }
}
