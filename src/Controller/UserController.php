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
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class UserController extends Controller
{
    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

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

        if ($request->get('_username') !== null) {
            $daoProvider = new DaoAuthenticationProvider(
                $userProvider,
                $userChecker,
                'main',
                $this->encoderFactory
            );

            $daoProvider->authenticate($unauthenticatedToken);
        }

        return new Response('Logged in');
    }

    public function logoutAction(Request $request)
    {
        $this->tokenStorage->setToken(null);
        
        return new Response('Logged out');
    }
}
