<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PiaApi\Entity\Oauth\User;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;

class RemoveUserCommand extends Command
{
    const NAME = 'pia:user:remove';

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
            ->setName(self::NAME)
            ->setDescription('Remove a user.')
            ->setHelp('This command allows you to remove a user for given email')
            ->addArgument('email', null, InputArgument::REQUIRED, 'The user\'s email')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);

        $email = $input->getArgument('email', null);

        if ($email === null) {
            $this->io->error('You must set an email and a password');

            return;
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($user === null) {
            $this->io->error(sprintf('There is no user with email « %s »', $email));

            return;
        }

        $this->removeUser($user);
    }

    public function removeUser($user)
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        $this->io->success(sprintf('User %s successfully removed !', $user->getEmail()));
    }
}
