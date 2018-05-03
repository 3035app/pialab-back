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
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use PiaApi\Form\Applications\CreateApplicationForm;
use PiaApi\Form\Applications\EditApplicationForm;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use PiaApi\Entity\Oauth\Client;
use FOS\OAuthServerBundle\Model\ClientManagerInterface;
use OAuth2\OAuth2;

class OauthController extends Controller
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
     *
     * @return void
     */
    public function manageApplicationsAction(Request $request)
    {
        if (!$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('login'));
        }
        
        $this->canAccess();

        $queryBuilder = $this->getDoctrine()->getRepository(Client::class)->createQueryBuilder('c');

        $queryBuilder
            ->orderBy('c.id', 'DESC');

        $adapter = new DoctrineORMAdapter($queryBuilder);

        $page = $request->get('page', 1);
        $limit = $request->get('limit', 20);

        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($limit);
        $pagerfanta->setCurrentPage($pagerfanta->getNbPages() < $page ? $pagerfanta->getNbPages() : $page);

        return $this->render('Applications/manageApplications.html.twig', [
            'applications' => $pagerfanta
        ]);
    }

    /**
     * @Route("/manageApplications/addApplication", name="manage_applications_add_application")
     *
     * @param Request $request
     *
     * @return void
     */
    public function addApplicationAction(Request $request)
    {
        $this->canAccess();

        $form = $this->createForm(CreateApplicationForm::class, [
            'allowedGrantTypes' => [
                OAuth2::GRANT_TYPE_IMPLICIT         => OAuth2::GRANT_TYPE_IMPLICIT,
                OAuth2::GRANT_TYPE_USER_CREDENTIALS => OAuth2::GRANT_TYPE_USER_CREDENTIALS,
                OAuth2::GRANT_TYPE_REFRESH_TOKEN    => OAuth2::GRANT_TYPE_REFRESH_TOKEN,
            ]
        ], [
            'action' => $this->generateUrl('manage_applications_add_application')
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $applicationData = $form->getData();

                $client = $this->fosOauthClientManager->createClient();
                /** @var Client $client */
                $client->setName($applicationData['name']);
                $client->setRedirectUris($applicationData['redirectUris']);
                $client->setAllowedGrantTypes($applicationData['allowedGrantTypes']);
                $this->fosOauthClientManager->updateClient($client);
            }
            return $this->redirect($this->generateUrl('manage_applications'));
        }

        return $this->render('Applications/createForm.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/manageUsers/addApplication/{applicationId}", name="manage_applications_edit_application")
     *
     * @param Request $request
     * @return void
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
            'action' => $this->generateUrl('manage_applications_edit_application', ['applicationId' => $user->getId()])
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $client = $form->getData();

            $this->getDoctrine()->getManager()->persist($client);
            $this->getDoctrine()->getManager()->flush();
            
            return $this->redirect($this->generateUrl('manage_applications'));
        }

        return $this->render('Applications/createForm.html.twig', [
            'form' => $form->createView()
        ]);
    }

    protected function canAccess()
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedHttpException();
        }
    }
}
