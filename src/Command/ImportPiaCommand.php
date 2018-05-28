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
use PiaApi\Entity\Pia\Structure;
use PiaApi\Entity\Pia\Pia;

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
            ->addOption('structure', null, InputOption::VALUE_OPTIONAL, 'The target structure (name or ID) where the PIA will be created')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Do the import without persisting values in database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);

        $file = $input->getArgument('jsonFile');
        $dryRun = $input->getOption('dry-run');
        $structureNameOrId = $input->getOption('structure');

        // Structure

        $structure = $this->fetchStructure($structureNameOrId);

        if ($structureNameOrId !== null && $structure === null) {
            $this->io->error(sprintf('Cannot find structure with name or id « %s », aborting', $structureNameOrId));

            return;
        }

        // Fetch file

        $fileContent = $this->fetchImportFile($file);

        if ($fileContent === null) {
            return;
        }

        // Transforming from json to entities

        $pia = $this->fetchDataAsEntites($fileContent, $structure);

        // Applying changes (or not if dry-run)

        if (!$dryRun && $this->io->confirm('confirm importing datas ?')) {
            $this->entityManager->persist($pia);
            $this->entityManager->flush($pia);

            $this->io->success(sprintf('Pia « %s » successfully imported !', $pia->getName()));
        }
    }

    private function fetchDataAsEntites($data, $structure)
    {
        /** @var Pia $pia */
        $pia = $this->jsonToEntityTransformer->transform($data);

        $pia->setStructure($structure);

        $this->io->table(
            [
                'Entity', 'Desccription', 'Number',
            ],
            [
                ['Pia', '"' . $pia->getName() . '", Structure #' . ($structure !== null ? $structure->getId() : 'N/A')],
                ['Comment', 'Pia\'s comments', $pia->getComments()->count()],
                ['Answer', 'Pia\'s answers', $pia->getAnswers()->count()],
                ['Evaluation', 'Pia\'s evaluations', $pia->getEvaluations()->count()],
                ['Measure', 'Pia\'s measures', $pia->getMeasures()->count()],
                ['Attachment', 'Pia\'s attachments', $pia->getAttachments()->count()],
            ]
        );

        return $pia;
    }

    private function fetchImportFile(string $file): ?string
    {
        $fileContent = '';
        try {
            $this->io->text(sprintf('Fetching file %s', $file));
            $fileContent = file_get_contents($file);

            if ($fileContent === false) {
                $this->io->error(sprintf('Cannot fetch file\'s content located under %s', $file));

                return null;
            }
        } catch (\Exception $e) {
            $this->io->error(sprintf('Cannot fetch file located under %s', $file));

            return null;
        }

        $this->io->text(sprintf('Transforming serialized datas to entities'));

        return $fileContent;
    }

    private function fetchStructure($nameOrId): ?Structure
    {
        return $this->entityManager->getRepository(Structure::class)->findOneByNameOrId($nameOrId);
    }
}
