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
use Symfony\Component\Console\Input\InputOption;
use PiaApi\Entity\Oauth\User;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;

class PromoteUserCommand extends Command
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
            ->setName('pia:user:promote')
            ->setDescription('Creates a new user.')
            ->setHelp('This command allows you to create a user for Pia Api')
            ->addArgument('email', null, InputArgument::REQUIRED, 'The user\'s email')
            ->addOption('role', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'The user\'s role')
            ->addOption('demote', null, InputOption::VALUE_NONE, 'Demoting the role(s)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);

        $email = $input->getArgument('email', null);

        if ($email === null) {
            $this->io->error('You must set an email');

            return;
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($user === null) {
            $this->io->error(sprintf('There is no user with email « %s »', $email));

            return;
        }

        $this->promoteUser($user, $input->getOption('role'), $input->getOption('demote'));
    }

    public function promoteUser($user, $roles, $demonting)
    {
        foreach ($roles as $index => $role) {
            if (!$demonting) {
                $user->addRole($role);
            } else {
                $user->removeRole($role);
            }
        }
        $this->entityManager->flush();

        $this->io->success(sprintf('User %s successfully %s !', $user->getEmail(), $demonting === false ? 'promoted' : 'demoted'));
    }
}
