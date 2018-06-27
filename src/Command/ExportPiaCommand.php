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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use PiaApi\DataExchange\Transformer\JsonToEntityTransformer;
use PiaApi\Entity\Pia\Structure;
use PiaApi\Entity\Pia\Pia;
use Symfony\Component\Filesystem\Filesystem;

class ExportPiaCommand extends Command
{
    const NAME = 'pia:export';

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
            ->setName(self::NAME)
            ->setDescription('Exports a Pia to a json file.')
            ->setHelp('This command allows you to export a Pia to a json file')
            ->addOption('pia', null, InputOption::VALUE_OPTIONAL, 'The PIA unique ID')
            ->addOption('structure', null, InputOption::VALUE_OPTIONAL, 'The Structure unique ID')
            ->addOption('outputPath', null, InputOption::VALUE_OPTIONAL, 'The target folder where to dump file(s). If left blank, json will be printed in console standard output')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);

        $piaId = $input->getOption('pia');
        $structureId = $input->getOption('structure');
        $outputPath = $input->getOption('outputPath');

        if ($outputPath === null) {
            $outputPath = 'php://stdout';
        }

        $pias = [];

        // Fetch PIAs

        if ($piaId !== null && $structureId !== null) {
            $this->io->error('You must set only a PIA Id OR a Structure Id, not both. Aborting');

            return;
        } elseif ($piaId === null && $structureId === null) {
            $this->io->error('You must set at least a PIA Id or a Structure Id. Aborting');

            return;
        } elseif ($piaId !== null) {
            $this->io->text(sprintf('Fetching unique PIA with ID %s', $piaId));
            $pias[] = $this->fetchPia($piaId);
        } elseif ($structureId !== null) {
            $this->io->text(sprintf('Fetching PIAs of Structure with ID %s', $structureId));
            $pias = $this->fetchStructure($structureId)->getPias()->toArray();
        }

        if (count($pias) === 0) {
            $this->io->error('No PIAs has been found. Aborting');

            return;
        } else {
            $this->io->text(sprintf('Found %s PIA(s)', count($pias)));
        }

        // Transforming from entities to json for each found PIAs

        foreach ($pias as $pia) {
            $this->io->text(sprintf('Exporting Pia #%s (%s)...', $pia->getId(), $pia->getName()));

            $serializedPia = $this->jsonToEntityTransformer->reverseTransform($pia);

            $fileName = sprintf('%s_export_pia_%s', (new \DateTime())->format('YmdHis'), $pia->getId());

            $outputFileName = $this->dumpIntoFile($serializedPia, $fileName, $outputPath);

            if ($outputFileName !== null) {
                $this->io->text(sprintf('Successfully exported Pia #%s to file %s', $pia->getId(), $outputFileName));
            }
        }

        $this->io->newLine(3);

        $this->io->success(sprintf('Successfully exported %d Pia(s)', count($pias)));
    }

    private function dumpIntoFile(string $json, string $fileName, ?string $outputPath): ?string
    {
        if ($outputPath !== 'php://stdout') {
            $fileSystem = new Filesystem();

            if (substr($fileName, -5) !== '.json') {
                $fileName .= '.json';
            }
            if (substr($outputPath, -1) !== '/') {
                $outputPath .= '/';
            }

            $fullPath = $outputPath . $fileName;

            $fileSystem->dumpFile($fullPath, $json);

            return $fullPath;
        } else {
            $this->io->section('PIA ' . $fileName);
            $this->io->text($json);

            return null;
        }
    }

    private function fetchPia($id): ?Pia
    {
        return $this->entityManager->getRepository(Pia::class)->find($id);
    }

    private function fetchStructure($nameOrId): ?Structure
    {
        return $this->entityManager->getRepository(Structure::class)->findOneByNameOrId($nameOrId);
    }
}
