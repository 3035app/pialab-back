<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use PiaApi\Entity\Oauth\User;
use PiaApi\Entity\Pia\UserProfile;
use PiaApi\Entity\Pia\Structure;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateUserCommand extends Command
{
    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var SymfonyStyle
     */
    protected $io;

    public function __construct(
        EncoderFactoryInterface $encoderFactory,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct();
        $this->encoderFactory = $encoderFactory;
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setName('pia:user:create')
            ->setDescription('Creates a new user.')
            ->setHelp('This command allows you to create a user for Pia Api')
            ->addOption('email', null, InputOption::VALUE_REQUIRED, 'The user\'s email')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'The user\'s password')
            ->addOption('firstname', null, InputOption::VALUE_OPTIONAL, 'The user\'s first name')
            ->addOption('lastname', null, InputOption::VALUE_OPTIONAL, 'The user\'s last name')
            ->addOption('structure', null, InputOption::VALUE_OPTIONAL, 'The user\'s structure')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);

        $email = $input->getOption('email');
        $password = $input->getOption('password');
        $structureName = $input->getOption('structure', null);
        $firstname = $input->getOption('firstname', null);
        $lastname = $input->getOption('lastname', null);

        $structRepo = $this->entityManager->getRepository(Structure::class);
        $structure = $structRepo->findOneByNameOrId($structureName);

        if ($structureName !== null && $structure === null) {
            $this->io->error('You must set an existing structure');

            return;
        }

        $user = new User($email, $password);
        $profile = new UserProfile();
        $user->setProfile($profile);

        if ($firstname !== null) {
            $profile->setFirstName($firstname);
        }
        if ($lastname !== null) {
            $profile->setLastName($lastname);
        }
        if ($structure !== null) {
            $user->setStructure($structure);
        }

        $encoder = $this->encoderFactory->getEncoder($user);
        $user->setPassword($encoder->encodePassword($user->getPassword(), $user->getSalt()));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->io->success(sprintf('User %s successfully created !', $user->getEmail()));
    }
}
