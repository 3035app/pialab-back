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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use PiaApi\DataExchange\Transformer\JsonToEntityTransformer;

class ImportPiaCommand extends Command
{
    /**
     * @var JsonToEntityTransformer
     */
    protected $jsonToEntityTransformer;
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var SymfonyStyle
     */
    protected $io;

    public function __construct(
        EntityManagerInterface $entityManager,
        JsonToEntityTransformer $jsonToEntityTransformer
    ) {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->jsonToEntityTransformer = $jsonToEntityTransformer;
    }

    protected function configure()
    {
        $this
            ->setName('pia:import')
            ->setDescription('Imports a Pia from a json file.')
            ->setHelp('This command allows you to import a Pia from a json file')
            ->addArgument('jsonFile', InputArgument::REQUIRED, 'The target json file to import. Could be an Url')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Do the import without persisting values in database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);

        $file = $input->getArgument('jsonFile');
        $dryRun = $input->getOption('dry-run');

        // Fetch file

        $fileContent = '';

        try {
            $this->io->text(sprintf('Fetching file %s', $file));
            $fileContent = file_get_contents($file);

            if ($fileContent === false) {
                $this->io->error(sprintf('Cannot fetch file\'s content located under %s', $file));
            }
        } catch (\Exception $e) {
            $this->io->error(sprintf('Cannot fetch file located under %s', $file));
        }

        // Transforming form json to entity

        $pia = $this->jsonToEntityTransformer->transform($fileContent);

        dump($pia);
    }
}
