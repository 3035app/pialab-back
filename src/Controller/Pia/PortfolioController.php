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
use PiaApi\Entity\Pia\Portfolio;
use PiaApi\Services\PortfolioService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as Swg;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use PiaApi\DataHandler\RequestDataHandler;
use PiaApi\Entity\Pia\Structure;
use PiaApi\Entity\Oauth\User;

class PortfolioController extends RestController
{
    /**
     * @var PortfolioService
     */
    private $portfolioService;

    public function __construct(
        PropertyAccessorInterface $propertyAccessor,
        PortfolioService $portfolioService
    ) {
        parent::__construct($propertyAccessor);
        $this->portfolioService = $portfolioService;
    }

    /**
     * Lists all portfolios.
     *
     * @Swg\Tag(name="Portfolio")
     *
     * @FOSRest\Get("/portfolios")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns all Portfolios",
     *     @Swg\Schema(
     *         type="array",
     *         @Swg\Items(ref=@Nelmio\Model(type=Portfolio::class, groups={"Default"}))
     *     )
     * )
     * @Security("is_granted('CAN_SHOW_PORTFOLIO')")
     *
     * @return array
     */
    public function listAction(Request $request)
    {
        $portfolios = $this->getUser()->getPortfolios();

        return $this->view($portfolios, Response::HTTP_OK);
    }

    /**
     * Shows one Portfolio by its ID.
     *
     * @Swg\Tag(name="Portfolio")
     *
     * @FOSRest\Get("/portfolios/{id}", requirements={"id"="\d+"})
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns one Portfolio",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Portfolio::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_SHOW_PORTFOLIO')")
     *
     * @return array
     */
    public function showAction(Request $request, $id)
    {
        $portfolio = $this->getResource($id);
        if ($portfolio === null) {
            return $this->view($portfolio, Response::HTTP_NOT_FOUND);
        }

        return $this->view($portfolio, Response::HTTP_OK);
    }

    /**
     * Creates a Portfolio.
     *
     * @Swg\Tag(name="Portfolio")
     *
     * @FOSRest\Post("/portfolios")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns the newly created Portfolio",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Portfolio::class, groups={"Default"})
     *     )
     * )
     *
     * @Security("is_granted('CAN_CREATE_PORTFOLIO')")
     *
     * @return array
     */
    public function createAction(Request $request)
    {
        $userId = $request->get('user', null);
        $user = $userId !== null ? $this->getRepository(User::class)->find($userId) : $this->getUser();

        $structureId = $request->get('structure', null);
        $structure = $structureId !== null ? $this->getRepository(Structure::class)->find($structureId) : $user->getStructure();

        $portfolio = $this->portfolioService->newPortfolio($request->get('name'));
        $portfolio->addUser($user);
        $user->addPortfolio($portfolio);

        if ($structure !== null) {
            $portfolio->addStructure($structure);
        }

        $this->persist($portfolio);

        return $this->view($portfolio, Response::HTTP_OK);
    }

    /**
     * Updates a Portfolio.
     *
     * @Swg\Tag(name="Portfolio")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Returns the updated Portfolio",
     *     @Swg\Schema(
     *         type="object",
     *         ref=@Nelmio\Model(type=Portfolio::class, groups={"Default"})
     *     )
     * )
     *
     * @FOSRest\Put("/portfolios/{id}", requirements={"id"="\d+"})
     *
     * @Security("is_granted('CAN_EDIT_PORTFOLIO')")
     *
     * @return array
     */
    public function updateAction(Request $request, $id)
    {
        $portfolio = $this->getResource($id);
        $this->canAccessResourceOr403($portfolio);

        $updatableAttributes = [
            'name' => RequestDataHandler::TYPE_STRING,
        ];

        $targetStructures = $request->get('structures', []);
        $portfolio->setStructures([]);
        foreach ($targetStructures as $structureData) {
            $portfolio->addStructure($this->getResource($structureData['id'], Structure::class));
        }

        $this->mergeFromRequest($portfolio, $updatableAttributes, $request);

        $this->update($portfolio);

        return $this->view($portfolio, Response::HTTP_OK);
    }

    /**
     * Deletes a Portfolio.
     *
     * @Swg\Tag(name="Portfolio")
     *
     * @Swg\Response(
     *     response=200,
     *     description="Empty content"
     * )
     *
     * @FOSRest\Delete("/portfolios/{id}", requirements={"id"="\d+"})
     *
     * @Security("is_granted('CAN_DELETE_PORTFOLIO')")
     *
     * @return array
     */
    public function deleteAction(Request $request, $id)
    {
        $portfolio = $this->getResource($id);
        $this->canAccessResourceOr403($portfolio);

        foreach ($portfolio->getStructures() as $structure) {
            $structure->setPortfolio(null);
        }

        $this->remove($portfolio);

        return $this->view([], Response::HTTP_OK);
    }

    protected function getEntityClass()
    {
        return Portfolio::class;
    }

    public function canAccessResourceOr403($resource): void
    {
        if ($this->isGranted('CAN_MANAGE_ONLY_OWNED_PORTFOLIOS') && in_array($resource, $this->getUser()->getPortfolios())) {
            throw new AccessDeniedHttpException('Resource not found');
        }
    }
}
