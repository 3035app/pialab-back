<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Service;

use FOS\OAuthServerBundle\Model\ClientInterface;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use PiaApi\Entity\Oauth\User;
use PiaApi\Entity\Pia\Structure;
use PiaApi\Entity\Pia\UserProfile;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use PiaApi\Repository\UserRepository;

class UserService
{
    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var StructureService
     */
    private $structureService;

    /**
     * @var ApplicationService
     */
    private $applicationService;

    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var TokenGeneratorInterface
     */
    private $tokenGenerator;

    public function __construct(
        UserRepository $userRepository,
        EncoderFactoryInterface $encoderFactory,
        StructureService $structureService,
        ApplicationService $applicationService,
        MailerInterface $mailer,
        TokenGeneratorInterface $tokenGenerator
    ) {
        $this->userRepository = $userRepository;
        $this->encoderFactory = $encoderFactory;
        $this->structureService = $structureService;
        $this->applicationService = $applicationService;
        $this->mailer = $mailer;
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * @return User
     */
    public function getById($id)
    {
        return $this->userRepository->find($id);
        //@todo throw exception if user is null
    }

    /**
     * @param string               $email
     * @param string               $password
     * @param Structure|null       $structure
     * @param ClientInterface|null $application
     *
     * @return User
     */
    public function newUser(string $email, string $password, ?Structure $structure = null, ?ClientInterface $application = null): User
    {
        $user = new User($email);

        $this->encodePassword($user, $password);

        $profile = new UserProfile();
        $profile->setUser($user);
        $user->setProfile($profile);

        if ($structure !== null) {
            $user->setStructure($structure);
        }

        if ($application !== null) {
            $user->setApplication($application);
        }

        return $user;
    }

    /**
     * Sends FOSUser reset password email.
     *
     * @param UserInterface $user
     */
    public function sendResettingEmail(UserInterface $user): void
    {
        if (null === $user->getConfirmationToken()) {
            $user->setConfirmationToken($this->tokenGenerator->generateToken());
        }
        $this->mailer->sendResettingEmailMessage($user);
        $user->setPasswordRequestedAt(new \DateTime());
        $this->userRepository->save($user);
    }

    /**
     * Encodes current user password.
     *
     * @param User   $user
     * @param string $password
     */
    public function encodePassword(User $user, string $password): void
    {
        $encoder = $this->encoderFactory->getEncoder($user);
        $user->setPassword($encoder->encodePassword($password, $user->getSalt()));
    }
}
