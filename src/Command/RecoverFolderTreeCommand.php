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
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Bridge\Doctrine\RegistryInterface;
use PiaApi\Entity\Pia\Folder;

class RecoverFolderTreeCommand extends Command
{
    const NAME = 'pia:folders:recover';

    /**
     * @var RegistryInterface
     */
    protected $doctrine;

    /**
     * @var SymfonyStyle
     */
    protected $io;

    public function __construct(
        RegistryInterface $doctrine
    ) {
        parent::__construct();
        $this->doctrine = $doctrine;
    }

    protected function configure()
    {
        $this
            ->setName(self::NAME)
            ->setDescription('Recover the whole folder trees.')
            ->setHelp('This command allows you to clean up the folders nested set tree')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);

        $repo = $this->doctrine->getRepository(Folder::class);

        $repo->verify();
        $repo->recover();

        $this->doctrine->getManager()->flush();
    }
}
