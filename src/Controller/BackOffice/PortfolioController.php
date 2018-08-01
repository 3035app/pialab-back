<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Controller\BackOffice;

use PiaApi\Entity\Pia\Portfolio;
use PiaApi\Entity\Pia\Structure;
use PiaApi\Form\Portfolio\CreatePortfolioForm;
use PiaApi\Form\Portfolio\EditPortfolioForm;
use PiaApi\Form\Portfolio\RemovePortfolioForm;
use PiaApi\Form\Structure\CreateStructureForm;
use PiaApi\Form\Structure\StructurePortfolioAssocForm;
use PiaApi\Services\PortfolioService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PortfolioController extends BackOfficeAbstractController
{
    /**
     * @var PortfolioService
     */
    private $portfolioService;

    public function __construct(PortfolioService $portfolioService)
    {
        $this->portfolioService = $portfolioService;
    }

    /**
     * @Route("/managePortfolios", name="manage_portfolios")
     * @Security("is_granted('CAN_SHOW_PORTFOLIO')")
     */
    public function managePortfoliosAction(Request $request)
    {
        $pagerfanta = null;
        if ($this->isGranted('CAN_MANAGE_ONLY_OWNED_PORTFOLIOS')) {
            $user = $this->getUser();
            $pagerfanta = $this->getDoctrine()
              ->getRepository(Portfolio::class)
              ->getPaginatedByUser($user);
        } else {
            $pagerfanta = $this->buildPager($request, Portfolio::class);
        }

        $page = $request->get('page', 1);
        $limit = $request->get('limit', $pagerfanta->getMaxPerPage());

        $pagerfanta->setMaxPerPage($limit);
        $pagerfanta->setCurrentPage($pagerfanta->getNbPages() < $page ? $pagerfanta->getNbPages() : $page);

        return $this->render('pia/Portfolio/managePortfolios.html.twig', [
            'portfolios' => $pagerfanta,
        ]);
    }

    /**
     * @Route("/showPortfolio/{portfolioId}", name="manage_portfolios_show_portfolio")
     * @Security("is_granted('CAN_SHOW_PORTFOLIO')")
     */
    public function showPortfolioAction(Request $request)
    {
        $portfolioId = $request->get('portfolioId');
        $portfolio = $this->getDoctrine()->getRepository(Portfolio::class)->find($portfolioId);

        if ($portfolio === null) {
            throw new NotFoundHttpException(sprintf('Portfolio « %s » does not exist', $portfolioId));
        }

        $structurePager = $this->getDoctrine()
            ->getRepository(Structure::class)
            ->getPaginatedStructuresByPortfolio($portfolio);

        $structurePage = $request->get('page', 1);
        $structureLimit = $request->get('limit', $structurePager->getMaxPerPage());

        $structurePager->setMaxPerPage($structureLimit);
        $structurePager->setCurrentPage($structurePager->getNbPages() < $structurePage ? $structurePager->getNbPages() : $structurePage);

        $structureForm = $this->createForm(CreateStructureForm::class, [], [
            'action'    => $this->generateUrl('manage_structures_add_structure'),
            'portfolio' => $portfolio,
            'redirect'  => $this->generateUrl('manage_portfolios_show_portfolio', ['portfolioId' => $portfolioId]),
        ]);

        return $this->render('pia/Portfolio/showPortfolio.html.twig', [
            'portfolio'      => $portfolio,
            'structures'     => $structurePager,
            'structureForm'  => $structureForm->createView(),
        ]);
    }

    /**
     * @Route("/managePortfolios/addPortfolio", name="manage_portfolios_add_portfolio")
     * @Security("is_granted('CAN_CREATE_PORTFOLIO')")
     *
     * @param Request $request
     */
    public function addPortfolioAction(Request $request)
    {
        $form = $this->createForm(CreatePortfolioForm::class, [], [
            'action' => $this->generateUrl('manage_portfolios_add_portfolio'),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $portfolio = $this->portfolioService->newFromFormData($data);

            $this->getDoctrine()->getManager()->persist($portfolio);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('manage_portfolios'));
        }

        return $this->render('pia/Layout/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/managePortfolios/editPortfolio/{portfolioId}", name="manage_portfolios_edit_portfolio")
     * @Security("is_granted('CAN_EDIT_PORTFOLIO')")
     *
     * @param Request $request
     */
    public function editPortfolioAction(Request $request)
    {
        $id = $request->get('portfolioId');
        $portfolio = $this->portfolioService->getById($id);

        if ($portfolio === null) {
            throw new NotFoundHttpException(sprintf('Portfolio « %s » does not exist', $portfolioId));
        }

        $form = $this->createForm(EditPortfolioForm::class, $portfolio, [
            'action' => $this->generateUrl('manage_portfolios_edit_portfolio', ['portfolioId' => $portfolio->getId()]),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $portfolio = $form->getData();
            if (empty($form->get('structures')->getData())) {
                $portfolio->setStructures([]);
            }

            $this->portfolioService->save($portfolio);

            return $this->redirect($this->generateUrl('manage_portfolios'));
        }

        return $this->render('pia/Layout/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/managePortfolios/removePortfolio/{portfolioId}", name="manage_portfolios_remove_portfolio")
     * @Security("is_granted('CAN_DELETE_PORTFOLIO')")
     *
     * @param Request $request
     */
    public function removePortfolioAction(Request $request)
    {
        $id = $request->get('portfolioId');
        $portfolio = $this->portfolioService->getById($id);

        if ($portfolio === null) {
            throw new NotFoundHttpException(sprintf('Portfolio « %s » does not exist', $portfolioId));
        }

        $form = $this->createForm(RemovePortfolioForm::class, $portfolio, [
            'action' => $this->generateUrl('manage_portfolios_remove_portfolio', ['portfolioId' => $portfolio->getId()]),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $portfolio = $form->getData();

            $this->portfolioService->remove($portfolio);

            return $this->redirect($this->generateUrl('manage_portfolios'));
        }

        return $this->render('pia/Portfolio/removePortfolio.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/showPortfolio/{portfolioId}/assocStructures", name="manage_portfolios_assoc_structures")
     * @Security("is_granted('CAN_SHOW_PORTFOLIO')")
     *
     * @param Request $request
     */
    public function assocStructuresAction(Request $request)
    {
        $id = $request->get('portfolioId');
        $portfolio = $this->portfolioService->getById($id);

        $form = $this->createForm(StructurePortfolioAssocForm::class, [], [
            'action'    => $this->generateUrl('manage_portfolios_assoc_structures', ['portfolioId' => $portfolio->getId()]),
            'portfolio' => $portfolio,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            foreach ($data['structures'] as $structure) {
                $portfolio->addStructure($structure);
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('manage_portfolios_show_portfolio', ['portfolioId' => $portfolio->getId()]));
        }

        return $this->render('pia/Layout/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
