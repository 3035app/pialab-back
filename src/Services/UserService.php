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
     * @param string               $email
     * @param string               $password
     * @param Structure|null       $structure
     * @param ClientInterface|null $application
     * @param string|null          $username
     *
     * @return User
     */
    public function createUser(
        string $email,
        string $password,
        ?Structure $structure = null,
        ?ClientInterface $application = null,
        ?string $username = null
    ): User {
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

        $user->setUsername($username ?? $this->generateUsername($user));

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

    /**
     * Generates a username.
     *
     * @param User $user
     *
     * @return string
     */
    public function generateUsername(User $user): string
    {
        // TODO: Check if duplicates already exists in databse before serving a username.
        $emailParts = explode('@', $user->getEmail());
        $str = preg_replace('/[^a-z0-9]+/i', ' ', $emailParts[0]);

        return '@' . str_replace(' ', '', ucwords($str));
    }
}
