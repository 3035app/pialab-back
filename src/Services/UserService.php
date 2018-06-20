<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Services;

use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use PiaApi\Entity\Oauth\User;
use FOS\OAuthServerBundle\Model\ClientInterface;
use PiaApi\Entity\Pia\Structure;
use Symfony\Bridge\Doctrine\RegistryInterface;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use PiaApi\Entity\Pia\UserProfile;

class UserService extends AbstractService
{
    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

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
        RegistryInterface $doctrine,
        EncoderFactoryInterface $encoderFactory,
        StructureService $structureService,
        ApplicationService $applicationService,
        MailerInterface $mailer,
        TokenGeneratorInterface $tokenGenerator
    ) {
        parent::__construct($doctrine);

        $this->encoderFactory = $encoderFactory;
        $this->structureService = $structureService;
        $this->applicationService = $applicationService;
        $this->mailer = $mailer;
        $this->tokenGenerator = $tokenGenerator;
    }

    public function getEntityClass(): string
    {
        return User::class;
    }

    /**
     * @param string      $email
     * @param string      $password
     * @param string|null $structureName
     * @param string|null $applicationName
     *
     * @return User
     */
    public function createUser(string $email, string $password, ?string $structureName = null, ?string $applicationName = null): User
    {
        $user = new User($email);

        $this->encodePassword($user, $password);

        if ($structureName !== null) {
            $structure = $this->structureService->getRepository()->findOneBy(['name' => $structureName]);

            if ($structure !== null) {
                $user->setStructure($structure);
            }
        }

        if ($applicationName !== null) {
            $application = $this->applicationService->getRepository()->findOneBy(['name' => $applicationName]);

            if ($application !== null) {
                $user->setApplication($application);
            }
        }

        $profile = new UserProfile();
        $profile->setUser($user);
        $user->setProfile($profile);

        return $user;
    }

    /**
     * @param string               $email
     * @param string               $password
     * @param ClientInterface|null $application
     *
     * @return User
     */
    public function createUserForStructure(string $email, string $password, ?Structure $structure = null): User
    {
        $user = $this->createUser($email, $password);
        if ($structure !== null) {
            $user->setStructure($structure);
        }

        return $user;
    }

    /**
     * @param string               $email
     * @param string               $password
     * @param Structure|null       $structure
     * @param ClientInterface|null $application
     *
     * @return User
     */
    public function createUserForStructureAndApplication(string $email, string $password, ?Structure $structure = null, ?ClientInterface $application = null): User
    {
        $user = $this->createUserForStructure($email, $password, $structure);
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
        $this->doctrineRegistry->getManager()->flush($user);
    }

    /**
     * Encodes current user password.
     *
     * @param User   $user
     * @param string $password
     */
    public function encodePassword(User &$user, string $password): void
    {
        $encoder = $this->encoderFactory->getEncoder($user);
        $user->setPassword($encoder->encodePassword($password, $user->getSalt()));
    }
}
