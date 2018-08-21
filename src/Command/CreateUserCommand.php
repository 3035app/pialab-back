<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Command;

use Doctrine\ORM\EntityManagerInterface;
use PiaApi\Entity\Oauth\User;
use PiaApi\Entity\Pia\Structure;
use PiaApi\Services\UserService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use PiaApi\Entity\Oauth\Client;

class CreateUserCommand extends Command
{
    const NAME = 'pia:user:create';

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

    /**
     * @var UserService
     */
    private $userService;

    public function __construct(
        EncoderFactoryInterface $encoderFactory,
        EntityManagerInterface $entityManager,
        UserService $userService
    ) {
        parent::__construct();

        $this->encoderFactory = $encoderFactory;
        $this->entityManager = $entityManager;
        $this->userService = $userService;
    }

    protected function configure()
    {
        $this
            ->setName(self::NAME)
            ->setDescription('Creates a new user.')
            ->setHelp('This command allows you to create a user for Pia Api')
            ->addArgument('email', InputArgument::OPTIONAL, 'The user\'s email')
            ->addArgument('password', InputArgument::OPTIONAL, 'The user\'s password')
            ->addOption('email', null, InputOption::VALUE_REQUIRED, 'The user\'s email')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'The user\'s password')
            ->addOption('username', null, InputOption::VALUE_REQUIRED, 'The user\'s username (alias)')
            ->addOption('firstName', null, InputOption::VALUE_REQUIRED, 'The user\'s first name')
            ->addOption('lastName', null, InputOption::VALUE_REQUIRED, 'The user\'s last name')
            ->addOption('structure', null, InputOption::VALUE_REQUIRED, 'The user\'s structure')
            ->addOption('application', null, InputOption::VALUE_REQUIRED, 'The user\'s application')
            ->addOption('sendResetEmail', false, InputOption::VALUE_NONE, 'Sends resetting password email to the user')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $this->io = new SymfonyStyle($input, $output);

        if ($input->getOption('email') !== null || $input->getOption('password') !== null) {
            $this->io->note([
                'Using options --email and --password is now deprecated.',
                'Please set email and password as arguments instead',
                'e.g. :bin/console pia:user:create your.email@email.com YourPassword',
            ]);

            $email = $input->getOption('email');
            $password = $input->getOption('password');
        } elseif ($input->getArgument('email') !== null && $input->getArgument('password') !== null) {
            $email = $input->getArgument('email');
            $password = $input->getArgument('password');
        } else {
            $this->io->error('You must set email and password');

            return 42;
        }

        $structureName = $input->getOption('structure', null);
        $appName = $input->getOption('application', null);
        $firstName = $input->getOption('firstName', null);
        $lastName = $input->getOption('lastName', null);
        $username = $input->getOption('username', null);

        $structure = null;
        $application = null;

        if ($structureName !== null) {
            $structure = $this->entityManager->getRepository(Structure::class)->findOneBy(['name' => $structureName]);

            if ($structure === null) {
                $this->io->error(sprintf('Structure with name « %s » was not found', $structureName));

                return 42;
            }
        }

        if ($appName !== null) {
            $application = $this->entityManager->getRepository(Client::class)->findOneBy(['name' => $appName]);

            if ($application === null) {
                $this->io->error(sprintf('Application with name « %s » was not found', $appName));

                return 42;
            }
        }

        $user = $this->userService->createUser(
            $email,
            $password,
            $structure,
            $application,
            $username
        );

        if ($firstName !== null) {
            $user->getProfile()->setFirstName($firstName);
        }
        if ($lastName !== null) {
            $user->getProfile()->setLastName($lastName);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        if ($input->getOption('sendResetEmail') === true) {
            $this->userService->sendResettingEmail($user);
        }

        $this->io->success(sprintf('User %s successfully created !', $user->getEmail()));

        return 0;
    }
}
