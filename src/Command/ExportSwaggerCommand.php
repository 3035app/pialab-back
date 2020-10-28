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
use Symfony\Component\Console\Style\SymfonyStyle;
use Nelmio\ApiDocBundle\ApiDocGenerator;
use Symfony\Component\Filesystem\Filesystem;

class ExportSwaggerCommand extends Command
{
    const NAME = 'api:export-swagger';

    /**
     * @var SymfonyStyle
     */
    protected $io;

    /**
     * @var ApiDocGenerator
     */
    protected $apiDocGenerator;

    public function __construct(
        ApiDocGenerator $apiDocGenerator
    ) {
        parent::__construct();
        $this->apiDocGenerator = $apiDocGenerator;
    }

    protected function configure()
    {
        $this
            ->setName(self::NAME)
            ->setDescription('Exports API doc as swagger.json')
            ->addOption('output', null, InputOption::VALUE_OPTIONAL, 'The target folder where to dump the file')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);

        $outputPath = $input->getOption('output');

        if ($outputPath === null) {
            $outputPath = 'php://stdout';
        }

        $fullPath = $this->dumpIntoFile(json_encode($this->apiDocGenerator->generate()->toArray()), $outputPath);

        $this->io->success(sprintf('Successfully exported in %s ', $fullPath));
    }

    private function dumpIntoFile(string $json, string $outputPath): ?string
    {
        if ($outputPath !== 'php://stdout') {
            $fileSystem = new Filesystem();
            $fileName = 'swagger.json';
            $fullPath = $outputPath . '/' . $fileName;
            $fileSystem->dumpFile($fullPath, $json);

            return $fullPath;
        } else {
            $this->io->section('API ' . $fileName);
            $this->io->text($json);

            return null;
        }
    }
}
