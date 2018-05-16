<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PiaApi\Entity\Oauth\User;
use PiaApi\Auth\UserProvider;
use PiaApi\Auth\UserChecker;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use PiaApi\Form\User\CreateUserForm;
use PiaApi\Form\User\EditUserForm;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController extends Controller
{
    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(EncoderFactoryInterface $encoderFactory, TokenStorageInterface $tokenStorage)
    {
        $this->encoderFactory = $encoderFactory;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Route("/login", name="login")
     *
     * @param Request             $request
     * @param AuthenticationUtils $authenticationUtils
     * @param UserProvider        $userProvider
     * @param UserChecker         $userChecker
     */
    public function loginAction(
        Request $request,
        AuthenticationUtils $authenticationUtils,
        UserProvider $userProvider,
        UserChecker $userChecker
    ) {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $username = $request->get('_username');

        if (!$request->isXmlHttpRequest()) {
            if ($this->getUser() !== null) {
                return $this->redirect($request->headers->get('refer', '/manageUsers'));
            }

            return $this->render('User/login.html.twig', [
                'error'         => $error,
                'last_username' => $lastUsername,
            ]);
        }

        return new Response('Logged in');
    }

    /**
     * @Route("/logout", name="logout")
     *
     * @param Request $request
     */
    public function logoutAction(Request $request)
    {
        $this->tokenStorage->setToken(null);

        return $this->redirect($this->generateUrl('login'));
    }

    /**
     * @Route("/manageUsers", name="manage_users")
     */
    public function manageUsersAction(Request $request)
    {
        if (!$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('login'));
        }

        $this->canAccess();

        $queryBuilder = $this->getDoctrine()->getRepository(User::class, 'oauth')->createQueryBuilder('u');

        $queryBuilder
            ->orderBy('u.id', 'DESC');

        $adapter = new DoctrineORMAdapter($queryBuilder);

        $page = $request->get('page', 1);
        $limit = $request->get('limit', 20);

        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($limit);
        $pagerfanta->setCurrentPage($pagerfanta->getNbPages() < $page ? $pagerfanta->getNbPages() : $page);

        return $this->render('User/manageUsers.html.twig', [
            'users' => $pagerfanta,
        ]);
    }

    /**
     * @Route("/manageUsers/addUser", name="manage_users_add_user")
     *
     * @param Request $request
     */
    public function addUserAction(Request $request)
    {
        $this->canAccess();

        $form = $this->createForm(CreateUserForm::class, ['roles' => ['ROLE_USER']], [
            'action' => $this->generateUrl('manage_users_add_user'),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userData = $form->getData();

            $user = new User($userData['email'], $userData['password']);
            foreach ($userData['roles'] as $role) {
                $user->addRole($role);
            }

            $encoder = $this->encoderFactory->getEncoder($user);
            $user->setPassword($encoder->encodePassword($userData['password'], $user->getSalt()));

            $user->setApplication($userData['application']);

            $this->getDoctrine()->getManager('oauth')->persist($user);
            $this->getDoctrine()->getManager('oauth')->flush();

            return $this->redirect($this->generateUrl('manage_users'));
        }

        return $this->render('form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/manageUsers/editUser/{userId}", name="manage_users_edit_user")
     *
     * @param Request $request
     */
    public function editUserAction(Request $request)
    {
        $this->canAccess();

        $userId = $request->get('userId');
        $user = $this->getDoctrine()->getRepository(User::class, 'oauth')->find($userId);

        if ($user === null) {
            throw new NotFoundHttpException(sprintf('User « %s » does not exist', $userId));
        }

        $form = $this->createForm(EditUserForm::class, $user, [
            'action' => $this->generateUrl('manage_users_edit_user', ['userId' => $user->getId()]),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $this->getDoctrine()->getManager('oauth')->persist($user);
            $this->getDoctrine()->getManager('oauth')->flush();

            return $this->redirect($this->generateUrl('manage_users'));
        }

        return $this->render('User/createForm.html.twig', [
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
