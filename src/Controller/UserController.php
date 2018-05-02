<?php

namespace PiaApi\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use PiaApi\Entity\User;
use PiaApi\Auth\UserProvider;
use PiaApi\Auth\UserChecker;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

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
     * @param Request $request
     * @param AuthenticationUtils $authenticationUtils
     * @param UserProvider $userProvider
     * @param UserChecker $userChecker
     *
     * @return void
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
                'error' => $error,
                'last_username' => $lastUsername,
            ]);
        }

        return new Response('Logged in');
    }

    /**
     * @Route("/logout", name="logout")
     *
     * @param Request $request
     * @return void
     */
    public function logoutAction(Request $request)
    {
        $this->tokenStorage->setToken(null);
        
        return new Response('Logged out');
    }

    /**
     * @Route("/manageUsers", name="manage_users")
     *
     * @return void
     */
    public function manageUsersAction()
    {
        if (!$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('login'));
        }
        
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedHttpException();
        }

        return $this->render('User/manageUsers.html.twig');
    }
}
