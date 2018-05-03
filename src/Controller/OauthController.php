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
use PiaApi\Form\User\CreateUserForm;
use PiaApi\Form\User\EditUserForm;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use PiaApi\Entity\Oauth\Client;

class OauthController extends Controller
{
    public function __construct()
    {
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

        $adapter = new DoctrineORMAdapter($queryBuilder);

        $page = $request->get('page', 1);

        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(20);
        $pagerfanta->setCurrentPage($pagerfanta->getNbPages() < $page ? $pagerfanta->getNbPages() : $page);

        return $this->render('Applications/manageApplications.html.twig', [
            'users' => $pagerfanta
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

        $form = $this->createForm(CreateUserForm::class, ['roles' => ['ROLE_USER']], [
            'action' => $this->generateUrl('manage_users_add_user')
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userData = $form->getData();

            $user = new User($userData['email'], $userData['password']);
            foreach ($userData['roles'] as $role) {
                $user->addRole($role);
            }

            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();
            
            return $this->redirect($this->generateUrl('manage_users'));
        }

        return $this->render('User/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/manageUsers/editUser/{userId}", name="manage_users_edit_user")
     *
     * @param Request $request
     * @return void
     */
    public function editUserAction(Request $request)
    {
        $this->canAccess();

        $userId = $request->get('userId');
        $user = $this->getDoctrine()->getRepository(User::class)->find($userId);

        if ($user === null) {
            throw new NotFoundHttpException(sprintf('User « %s » does not exist', $userId));
        }

        $form = $this->createForm(EditUserForm::class, $user, [
            'action' => $this->generateUrl('manage_users_edit_user', ['userId' => $user->getId()])
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();
            
            return $this->redirect($this->generateUrl('manage_users'));
        }

        return $this->render('User/form.html.twig', [
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
