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
use PiaApi\DataExchange\Transformer\JsonToEntityTransformer;
use PiaApi\Entity\Pia\Pia;
use PiaApi\Entity\Pia\ProcessingTemplate;
use PiaApi\Entity\Pia\Structure;
use PiaApi\Services\ProcessingTemplateService;
use splitbrain\PHPArchive\Tar;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ProcessingTemplatesBatchImportCommand extends Command
{
    const NAME = 'pia:templates:batch-import';

    /**
     * @var JsonToEntityTransformer
     */
    protected $jsonToEntityTransformer;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var ProcessingTemplateService
     */
    protected $processingTemplateService;

    /**
     * @var SymfonyStyle
     */
    protected $io;

    /**
     * @var string
     */
    private $temporaryExtractFolder = '/tmp/PIA-Templates-import';

    public function __construct(
        EntityManagerInterface $entityManager,
        JsonToEntityTransformer $jsonToEntityTransformer,
        ProcessingTemplateService $processingTemplateService
    ) {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->jsonToEntityTransformer = $jsonToEntityTransformer;
        $this->processingTemplateService = $processingTemplateService;
    }

    protected function configure()
    {
        $this
            ->setName(self::NAME)
            ->setDescription('Imports a collection of processing templates into backend')
            ->setHelp('This command allows you to import all json files included in a specific folder as PIA templates')
            ->addArgument('path', InputArgument::REQUIRED, 'The target directory (or archive) that contains templates json files (can be relative or absolute)')
            ->addOption('enable-all', null, InputOption::VALUE_NONE, 'Does the templates imported should be enabled ? By default, all templates are disabled')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Do the import without persisting values in database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
        $templatesFolder = $input->getArgument('path');
        $enableAll = $input->getOption('enable-all');
        $dryRun = $input->getOption('dry-run');
        $noInteract = $input->getOption('no-interaction');

        if (!is_dir($templatesFolder)) {
            $templatesFolder = $this->extractArchive($templatesFolder);
        }

        // Fetch file
        $this->io->note(sprintf('Importing files under folder « %s »', $templatesFolder));
        $fileInfos = $this->fetchImportFilesPaths($templatesFolder);

        if (count($fileInfos) === 0) {
            $this->io->error(sprintf('No json file has been found into folder %s', $templatesFolder));

            return;
        }

        // Transforming from json to entities

        if ($noInteract || $this->io->confirm(sprintf('confirm importing %d template(s) ?', count($fileInfos)))) {
            foreach ($fileInfos as $fileInfo) {
                $template = $this->buildTemplate($fileInfo['path'], $fileInfo['filename'], $enableAll);

                if ($dryRun) {
                    continue;
                }

                $this->entityManager->persist($template);
                $this->entityManager->flush($template);
            }
        }
        $this->io->success(sprintf(' %d templates imported', count($fileInfos)));
    }

    private function extractArchive(string $archivePath): string
    {
        $tempDir = sprintf(
            '%s_%s',
            $this->temporaryExtractFolder,
            (new \DateTime('NOW'))->format('Ymd-His-u')
        );

        $this->io->note([
            sprintf('extract templates from archive « %s » to path « %s »', $archivePath, $tempDir),
        ]);

        $tar = new Tar();
        $tar->open($archivePath);
        $tar->extract($tempDir);

        return $tempDir;
    }

    private function buildTemplate(string $filePath, string $fileName, bool $enabled = false): ProcessingTemplate
    {
        $templateJson = file_get_contents($filePath);

        // Fetching template json as entity in order to get target template name only
        $pia = $this->fetchDataAsEntities($templateJson);

        $template = $this->processingTemplateService->createTemplate(
            $pia->getName(),
            $templateJson,
            $fileName
        );

        $template->setEnabled($enabled);

        $this->io->comment(
            sprintf('[FILE]   : %s', $filePath) .
            "\n" .
            sprintf('[NAME]   : %s', $template->getName()) .
            "\n" .
            sprintf('[ENABLED]: %s', $enabled ? 'true' : 'false')
        );

        return $template;
    }

    private function fetchDataAsEntities($data): Pia
    {
        /** @var Pia $simplePia */
        $simplePia = $this->jsonToEntityTransformer->transform($data);

        // Cleaning up useless datas
        $simplePia->getAnswers()->clear();
        $simplePia->getAttachments()->clear();
        $simplePia->getComments()->clear();
        $simplePia->getEvaluations()->clear();
        $simplePia->getMeasures()->clear();

        return $simplePia;
    }

    private function fetchImportFilesPaths(string $templatesFolder): array
    {
        $files = [];

        $this->io->text(sprintf('Listing json files found in folder %s', $templatesFolder));

        $finder = new Finder();
        $finder
            ->files()
            ->filter(function (SplFileInfo $file) {
                return $file->getExtension() === 'json';
            })
            ->depth('< 1')
            ->in($templatesFolder);

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $files[] = [
                'path'     => $file->getRealPath(),
                'filename' => $file->getFilename(),
            ];
        }

        return $files;
    }

    private function fetchStructure($nameOrId): ?Structure
    {
        return $this->entityManager->getRepository(Structure::class)->findOneByNameOrId($nameOrId);
    }
}
