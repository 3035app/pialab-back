<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Controller\Pia;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use PiaApi\Entity\Pia\Portfolio;
use PiaApi\Services\PortfolioService;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

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
     * @FOSRest\Get("/portfolios")
     * @Security("is_granted('CAN_SHOW_PORTFOLIO')")
     *
     * @return View
     */
    public function listAction(Request $request)
    {
        $portfolios = $this->getUser()->getPortfolios();

        return $this->view($portfolios, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Get("/portfolios/{id}")
     * @Security("is_granted('CAN_SHOW_PORTFOLIO')")
     *
     * @return View
     */
    public function showAction(Request $request, $id)
    {
        $portfolio = $this->getResource($id);
        if ($portfolio === null) {
            return $this->view($portfolio, Response::HTTP_NOT_FOUND);
        }

        return $this->view($portfolio, Response::HTTP_OK);
    }

    protected function getEntityClass()
    {
        return Portfolio::class;
    }
}
