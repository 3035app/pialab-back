<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Controller\BackOffice;

use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use PiaApi\Entity\Oauth\User;
use PiaApi\Form\User\CreateUserForm;
use PiaApi\Form\User\EditUserForm;
use PiaApi\Form\User\RemoveUserForm;
use PiaApi\Form\User\SendResetPasswordEmailForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use PiaApi\Entity\Pia\UserProfile;
use Symfony\Component\Security\Core\User\UserInterface;

class UserController extends BackOfficeAbstractController
{
    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var int
     */
    private $retryTtl;

    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @var TokenGeneratorInterface
     */
    private $tokenGenerator;

    public function __construct(EncoderFactoryInterface $encoderFactory, TokenStorageInterface $tokenStorage, MailerInterface $mailer, int $FOSUserResettingRetryTTL, UserManagerInterface $userManager, TokenGeneratorInterface $tokenGenerator)
    {
        $this->encoderFactory = $encoderFactory;
        $this->tokenStorage = $tokenStorage;
        $this->retryTtl = $FOSUserResettingRetryTTL;
        $this->mailer = $mailer;
        $this->userManager = $userManager;
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * @Route("/manageUsers", name="manage_users")
     *
     * @param Request $request
     */
    public function manageUsersAction(Request $request)
    {
        if (!$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        $this->canAccess();

        $pagerfanta = $this->buildPager($request, User::class);

        return $this->render('pia/User/manageUsers.html.twig', [
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
            $user->setStructure($userData['structure']);

            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();

            if (isset($userData['sendResetingEmail'])) {
                $this->sendResetingEmail($user);
            }

            return $this->redirect($this->generateUrl('manage_users'));
        }

        return $this->render('pia/Layout/form.html.twig', [
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
        $user = $this->getDoctrine()->getRepository(User::class)->find($userId);

        if ($user === null) {
            throw new NotFoundHttpException(sprintf('User « %s » does not exist', $userId));
        }

        if ($user->getProfile() === null) {
            $profile = new UserProfile();
            $user->setProfile($profile);
        }

        $form = $this->createForm(EditUserForm::class, $user, [
            'action' => $this->generateUrl('manage_users_edit_user', ['userId' => $user->getId()]),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('manage_users'));
        }

        return $this->render('pia/User/editForm.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/manageUsers/removeUser/{userId}", name="manage_users_remove_user")
     *
     * @param Request $request
     */
    public function removeUserAction(Request $request)
    {
        $this->canAccess();

        $userId = $request->get('userId');
        $user = $this->getDoctrine()->getRepository(User::class)->find($userId);

        if ($user === null) {
            throw new NotFoundHttpException(sprintf('User « %s » does not exist', $userId));
        }

        if ($user === $this->getUser()) {
            throw new NotFoundHttpException('You cannot delete yourself !');
        }

        $form = $this->createForm(RemoveUserForm::class, $user, [
            'action' => $this->generateUrl('manage_users_remove_user', ['userId' => $user->getId()]),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $this->getDoctrine()->getManager()->remove($user);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('manage_users'));
        }

        return $this->render('pia/User/removeUser.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/manageUsers/sendResetPasswordEmail/{userId}", name="manage_users_send_reset_password_email")
     *
     * @param Request $request
     * @param string  $username
     */
    public function sendResetPasswordEmailAction(Request $request, $userId)
    {
        $this->canAccess();

        $user = $this->userManager->findUserBy(['id' => $userId]);

        if ($user === null) {
            throw new NotFoundHttpException(sprintf('User « %s » does not exist', $userId));
        }

        $form = $this->createForm(SendResetPasswordEmailForm::class, $user, [
            'action' => $this->generateUrl('manage_users_send_reset_password_email', ['userId' => $user->getId()]),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            // Uncomment this « if / endif » to apply the TTL between to email sending
            // if (!$user->isPasswordRequestNonExpired($this->retryTtl)) {

            $this->sendResetingEmail($user);

            return $this->redirect($this->generateUrl('manage_users'));

            // « endif »
            // }
        }

        return $this->render('pia/User/sendResetPasswordEmail.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Sends FOSUser reset password email.
     *
     * @param UserInterface $user
     */
    private function sendResetingEmail(UserInterface $user): void
    {
        if (null === $user->getConfirmationToken()) {
            $user->setConfirmationToken($this->tokenGenerator->generateToken());
        }
        $this->mailer->sendResettingEmailMessage($user);
        $user->setPasswordRequestedAt(new \DateTime());
        $this->userManager->updateUser($user);
    }

    protected function canAccess()
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedHttpException();
        }
    }
}
