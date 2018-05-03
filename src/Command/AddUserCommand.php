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
use PiaApi\Entity\User;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AddUserCommand extends Command
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
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);

        $email = $input->getOption('email', null);
        $password = $input->getOption('password', null);

        if ($email === null || $password === null) {
            $this->io->error('You must set an email and a password');
            return;
        }

        $this->addUser($email, $password);
    }

    public function addUser($email, $password)
    {
        $user = new User($email, $password);

        $encoder = $this->encoderFactory->getEncoder($user);
        $user->setPassword($encoder->encodePassword($user->getPassword(), $user->getSalt()));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->io->success(sprintf('User %s successfully created !', $user->getEmail()));
    }
}
