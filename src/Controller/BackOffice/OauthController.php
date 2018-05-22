<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Controller\BackOffice;

use FOS\OAuthServerBundle\Model\ClientManagerInterface;
use OAuth2\OAuth2;
use PiaApi\Entity\Oauth\Client;
use PiaApi\Form\Application\CreateApplicationForm;
use PiaApi\Form\Application\EditApplicationForm;
use PiaApi\Form\Application\RemoveApplicationForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OauthController extends BackOfficeAbstractController
{
    /**
     * @var ClientManagerInterface
     */
    protected $fosOauthClientManager;

    public function __construct(ClientManagerInterface $fosOauthClientManager)
    {
        $this->fosOauthClientManager = $fosOauthClientManager;
    }

    /**
     * @Route("/manageApplications", name="manage_applications")
     */
    public function manageApplicationsAction(Request $request)
    {
        if (!$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        $this->canAccess();

        $pagerfanta = $this->buildPager($request, Client::class);

        return $this->render('pia/Application/manageApplications.html.twig', [
            'applications' => $pagerfanta,
        ]);
    }

    /**
     * @Route("/manageApplications/addApplication", name="manage_applications_add_application")
     *
     * @param Request $request
     */
    public function addApplicationAction(Request $request)
    {
        $this->canAccess();

        $form = $this->createForm(CreateApplicationForm::class, [
            'allowedGrantTypes' => [
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

                $client = $this->fosOauthClientManager->createClient();
                /* @var Client $client */
                $client->setName($applicationData['name']);
                $client->setUrl($applicationData['url']);
                $client->setRedirectUris($applicationData['redirectUris']);
                $client->setAllowedGrantTypes($applicationData['allowedGrantTypes']);
                $this->fosOauthClientManager->updateClient($client);
            }

            return $this->redirect($this->generateUrl('manage_applications'));
        }

        return $this->render('pia/Application/createForm.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/manageApplications/editApplication/{applicationId}", name="manage_applications_edit_application")
     *
     * @param Request $request
     */
    public function editApplicationAction(Request $request)
    {
        $this->canAccess();

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
     *
     * @param Request $request
     */
    public function removeApplicationAction(Request $request)
    {
        $this->canAccess();

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

    protected function canAccess()
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedHttpException();
        }
    }
}
