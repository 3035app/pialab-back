<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Controller\BackOffice;

use OAuth2\OAuth2;
use PiaApi\Entity\Oauth\Client;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use PiaApi\Form\Application\CreateApplicationForm;
use PiaApi\Form\Application\EditApplicationForm;
use PiaApi\Form\Application\RemoveApplicationForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use PiaApi\Service\ApplicationService;

class ApplicationController extends BackOfficeAbstractController
{
    /**
     * @var ApplicationService
     */
    private $applicationService;

    public function __construct(ApplicationService $applicationService)
    {
        $this->applicationService = $applicationService;
    }

    /**
     * @Route("/manageApplications", name="manage_applications")
     * @Security("is_granted('CAN_SHOW_APPLICATION')")
     */
    public function manageApplicationsAction(Request $request)
    {
        $pagerfanta = $this->buildPager($request, Client::class);

        return $this->render('pia/Application/manageApplications.html.twig', [
            'applications' => $pagerfanta,
        ]);
    }

    /**
     * @Route("/manageApplications/addApplication", name="manage_applications_add_application")
     * @Security("is_granted('CAN_CREATE_APPLICATION')")
     *
     * @param Request $request
     */
    public function addApplicationAction(Request $request)
    {
        $form = $this->createForm(CreateApplicationForm::class, [
            'allowedGrantTypes' => [ // Sets default grant types checked on form
                OAuth2::GRANT_TYPE_IMPLICIT         => OAuth2::GRANT_TYPE_IMPLICIT,
                OAuth2::GRANT_TYPE_USER_CREDENTIALS => OAuth2::GRANT_TYPE_USER_CREDENTIALS,
                OAuth2::GRANT_TYPE_REFRESH_TOKEN    => OAuth2::GRANT_TYPE_REFRESH_TOKEN,
            ],
        ], [
            'action' => $this->generateUrl('manage_applications_add_application'),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $applicationData = $form->getData();

                $application = $this->applicationService->newApplication(
                    $applicationData['name'],
                    $applicationData['url'],
                    $applicationData['allowedGrantTypes']
                );
                $application->setRedirectUris($applicationData['redirectUris']);

                $this->applicationService->updateApplication($application);
            }

            return $this->redirect($this->generateUrl('manage_applications'));
        }

        return $this->render('pia/Application/createForm.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/manageApplications/editApplication/{applicationId}", name="manage_applications_edit_application")
     * @Security("is_granted('CAN_EDIT_APPLICATION')")
     *
     * @param Request $request
     */
    public function editApplicationAction(Request $request)
    {
        $userId = $request->get('applicationId');
        $user = $this->getDoctrine()->getRepository(Client::class)->find($userId);

        if ($user === null) {
            throw new NotFoundHttpException(sprintf('Application « %s » does not exist', $userId));
        }

        $form = $this->createForm(EditApplicationForm::class, $user, [
            'action' => $this->generateUrl('manage_applications_edit_application', ['applicationId' => $user->getId()]),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $client = $form->getData();

            $this->getDoctrine()->getManager()->persist($client);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('manage_applications'));
        }

        return $this->render('pia/Application/createForm.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/manageApplications/removeApplication/{applicationId}", name="manage_applications_remove_application")
     * @Security("is_granted('CAN_DELETE_APPLICATION')")
     *
     * @param Request $request
     */
    public function removeApplicationAction(Request $request)
    {
        $applicationId = $request->get('applicationId');
        $user = $this->getDoctrine()->getRepository(Client::class)->find($applicationId);

        if ($user === null) {
            throw new NotFoundHttpException(sprintf('Appllication « %s » does not exist', $applicationId));
        }

        $form = $this->createForm(RemoveApplicationForm::class, $user, [
            'action' => $this->generateUrl('manage_applications_remove_application', ['applicationId' => $user->getId()]),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $application = $form->getData();

            foreach ($application->getUsers() as $user) {
                $user->setApplication(null);
            }

            $this->getDoctrine()->getManager()->remove($application);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('manage_applications'));
        }

        return $this->render('pia/Application/removeApplication.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
