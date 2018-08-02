<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Controller\BackOffice;

use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use PiaApi\Entity\Oauth\User;
use PiaApi\Entity\Pia\UserProfile;
use PiaApi\Form\User\CreateUserForm;
use PiaApi\Form\User\EditUserForm;
use PiaApi\Form\User\RemoveUserForm;
use PiaApi\Form\User\SendResetPasswordEmailForm;
use PiaApi\Security\Role\RoleHierarchy;
use PiaApi\Services\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class UserController extends BackOfficeAbstractController
{
    /**
     * @var int
     */
    private $retryTtl;

    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var TokenGeneratorInterface
     */
    private $tokenGenerator;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var RoleHierarchy
     */
    private $roleHierarchy;

    public function __construct(
        MailerInterface $mailer,
        int $FOSUserResettingRetryTTL,
        TokenGeneratorInterface $tokenGenerator,
        UserService $userService,
        RoleHierarchy $roleHierarchy
    ) {
        $this->retryTtl = $FOSUserResettingRetryTTL;
        $this->mailer = $mailer;
        $this->tokenGenerator = $tokenGenerator;
        $this->userService = $userService;
        $this->roleHierarchy = $roleHierarchy;
    }

    /**
     * @Route("/manageUsers", name="manage_users")
     * @Security("is_granted('CAN_ACCESS_BACK_OFFICE') and is_granted('CAN_SHOW_USER')")
     *
     * @param Request $request
     */
    public function manageUsersAction(Request $request)
    {
        $userPager = null;
        if ($this->isGranted('CAN_MANAGE_ONLY_OWNED_USERS')) {
            $structure = $this->getUser()->getStructure();
            $userPager = $this->getDoctrine()
              ->getRepository(User::class)
              ->getReachableUsersPaginated($this->getUser());
        } else {
            $userPager = $this->buildPager($request, User::class);
        }

        $userPage = $request->get('page', 1);
        $userLimit = $request->get('limit', $userPager->getMaxPerPage());

        $userPager->setMaxPerPage($userLimit);
        $userPager->setCurrentPage($userPager->getNbPages() < $userPage ? $userPager->getNbPages() : $userPage);

        return $this->render('pia/User/manageUsers.html.twig', [
            'users' => $userPager,
        ]);
    }

    /**
     * @Route("/manageUsers/addUser", name="manage_users_add_user")
     * @Security("is_granted('CAN_ACCESS_BACK_OFFICE') and is_granted('CAN_CREATE_USER')")
     *
     * @param Request $request
     */
    public function addUserAction(Request $request)
    {
        $form = $this->createForm(CreateUserForm::class, ['roles' => ['ROLE_USER']], [
            'action'      => $this->generateUrl('manage_users_add_user'),
            'structure'   => $this->isGranted('CAN_MANAGE_STRUCTURES') || $this->isGranted('CAN_MANAGE_ONLY_OWNED_STRUCTURES') ? false : $this->getUser()->getStructure(),
            'application' => $this->isGranted('CAN_MANAGE_APPLICATIONS') || $this->isGranted('CAN_MANAGE_ONLY_OWNED_APPLICATIONS') ? false : $this->getUser()->getApplication(),
            'redirect'    => $this->getQueryRedirectUrl($request),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userData = $form->getData();

            $user = $this->userService->createUser(
                $userData['email'],
                $userData['password'],
                $userData['structure'],
                $userData['application']
            );

            foreach ($userData['roles'] as $role) {
                $user->addRole($role);
            }

            $user->setProfile($userData['profile']);

            $this->getDoctrine()->getManager()->persist($user);

            try {
                $this->getDoctrine()->getManager()->flush();
            } catch (UniqueConstraintViolationException $e) {
                $this->addFlash('error', 'pia.flashes.user_emails_must_be_unique');

                return $this->redirect($this->generateUrl('manage_users'));
            }

            if (isset($userData['sendResettingEmail']) && $userData['sendResettingEmail'] === true) {
                $this->userService->sendResettingEmail($user);
            }

            $customRedirect = $form->get('redirect')->getData();
            $redirectUrl = $customRedirect ?? $this->generateUrl('manage_users');

            return $this->redirect($redirectUrl);
        }

        return $this->render('pia/Layout/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/manageUsers/editUser/{userId}", name="manage_users_edit_user")
     * @Security("is_granted('CAN_ACCESS_BACK_OFFICE') and is_granted('CAN_EDIT_USER')")
     *
     * @param Request $request
     */
    public function editUserAction(Request $request, $userId)
    {
        $user = $this->userService->getRepository()->find($userId);

        if ($user === null) {
            throw new NotFoundHttpException(sprintf('User « %s » does not exist', $userId));
        }

        if ($user->getProfile() === null) {
            $profile = new UserProfile();
            $user->setProfile($profile);
        }

        $form = $this->createForm(EditUserForm::class, $user, [
            'action'       => $this->generateUrl('manage_users_edit_user', ['userId' => $user->getId()]),
            'structure'    => $this->isGranted('CAN_MANAGE_STRUCTURES') || $this->isGranted('CAN_MANAGE_ONLY_OWNED_STRUCTURES') ? false : $this->getUser()->getStructure(),
            'application'  => $this->isGranted('CAN_MANAGE_APPLICATIONS') || $this->isGranted('CAN_MANAGE_ONLY_OWNED_APPLICATIONS') ? false : $this->getUser()->getApplication(),
            'redirect'     => $this->getQueryRedirectUrl($request),
            'hasPortfolio' => $this->isGranted('CAN_MANAGE_PORTFOLIOS') && $this->roleHierarchy->isGranted($user, 'ROLE_SHARED_DPO'),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();

            $customRedirect = $form->get('redirect')->getData();
            $redirectUrl = $customRedirect ?? $this->generateUrl('manage_users');

            return $this->redirect($redirectUrl);
        }

        return $this->render('pia/User/editForm.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/manageUsers/removeUser/{userId}", name="manage_users_remove_user")
     * @Security("is_granted('CAN_ACCESS_BACK_OFFICE') and is_granted('CAN_DELETE_USER')")
     *
     * @param Request $request
     */
    public function removeUserAction(Request $request, $userId)
    {
        $user = $this->userService->getRepository()->find($userId);

        if ($user === null) {
            throw new NotFoundHttpException(sprintf('User « %s » does not exist', $userId));
        }

        if ($user === $this->getUser()) {
            throw new NotFoundHttpException('You cannot delete yourself !');
        }

        if (!$this->roleHierarchy->hasHigherRole($this->getUser(), $user)) {
            throw new NotFoundHttpException('You cannot delete user with an higher role !');
        }

        $form = $this->createForm(RemoveUserForm::class, $user, [
            'action'   => $this->generateUrl('manage_users_remove_user', ['userId' => $user->getId()]),
            'redirect' => $this->getQueryRedirectUrl($request),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $this->getDoctrine()->getManager()->remove($user);
            $this->getDoctrine()->getManager()->flush();

            $customRedirect = $form->get('redirect')->getData();
            $redirectUrl = $customRedirect ?? $this->generateUrl('manage_users');

            return $this->redirect($redirectUrl);
        }

        return $this->render('pia/User/removeUser.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/manageUsers/sendResetPasswordEmail/{userId}", name="manage_users_send_reset_password_email")
     * @Security("is_granted('CAN_ACCESS_BACK_OFFICE') and is_granted('CAN_SHOW_USER')")
     *
     * @param Request $request
     * @param string  $username
     */
    public function sendResetPasswordEmailAction(Request $request, $userId)
    {
        $user = $this->userService->getRepository()->find($userId);

        if ($user === null) {
            throw new NotFoundHttpException(sprintf('User « %s » does not exist', $userId));
        }

        $form = $this->createForm(SendResetPasswordEmailForm::class, $user, [
            'action' => $this->generateUrl('manage_users_send_reset_password_email', ['userId' => $user->getId()]),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            // Uncomment this « if / endif » to apply the TTL allowed between two emails request
            // if (!$user->isPasswordRequestNonExpired($this->retryTtl)) {

            $this->userService->sendResettingEmail($user);

            return $this->redirect($this->generateUrl('manage_users'));

            // « endif »
            // }
        }

        return $this->render('pia/User/sendResetPasswordEmail.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
