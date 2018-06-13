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
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class MergeMigrationsCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var string
     */
    private $doctrineMigrationPath;

    private $majorMigrationSkeltonName = 'majorMigration.skeleton';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('doctrine:migrations:merge')
            ->setDescription('Merges dev migrations into single one')
            ->addArgument(
                'tag',
                InputArgument::REQUIRED,
                'The target Tag number (dots will be converted to underscores)'
            )
            ->setHelp(<<<EOT
The <info>%command.name%</info> command will merge all migrations generated with 
doctrine:migrations:diff or doctrine:migrations:generate commands to one correspondig to a specific tag name.

<info>php %command.full_name% [--tag=...]</info>

EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $this->init();

        $io->title('Merge Doctrine Migrations');

        $this->handlingExistingMigrations($input->getArgument('tag'));

        return 0;
    }

    private function init()
    {
        $this->doctrineMigrationPath = $this->container->getParameter('doctrine_migrations.dir_name');
        $doctrineMigrationNamespace = $this->container->getParameter('doctrine_migrations.namespace');

        $finder = new Finder();
        $finder->files()->in($this->doctrineMigrationPath)->name('/^Version[0-9]{14}\.php/');

        // Loading migrations classes

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            include $file->getRealPath();
        }
    }

    private function handlingExistingMigrations(string $tag): void
    {
        $migrations = array_filter(get_declared_classes(), function ($class) use ($doctrineMigrationNamespace) {
            preg_match('/^' . $doctrineMigrationNamespace . '\\\/', $class, $found);

            return count($found) > 0;
        });

        $versions = [];

        foreach ($migrations as $migrationClass) {
            $reflexionClass = new \ReflectionClass($migrationClass);

            $versionCode = str_replace('Version', '', $reflexionClass->getShortName());

            $fn = [
                'up'   => null,
                'down' => null,
            ];

            // Handling up method

            $up = $reflexionClass->getMethod('up');

            $fn['up'] = [
                'filePath'  => $reflexionClass->getFileName(),
                'startLine' => $up->getStartLine(),
                'endLine'   => $up->getEndLine(),
                'body'      => '',
            ];

            $fn['up']['body'] = implode('', array_slice(file($fn['up']['filePath']), $fn['up']['startLine'], ($fn['up']['endLine'] - $fn['up']['startLine']) + 1));

            // Handling down method

            $down = $reflexionClass->getMethod('down');

            $fn['down'] = $this->getMethodInfos($down);

            $versions[$versionCode] = $fn;
        }

        ksort($versions);

        $this->createTargetMigration($tag, $versions);
    }

    private function createTargetMigration(string $tag, $versions): void
    {
        $migrationSkeleton = $this->doctrineMigrationPath . '/Lib/' . $this->majorMigrationSkeltonName;

        $content = file_get_contents($migrationSkeleton);

        $content = str_replace('<version_tag>', str_replace('.', '_', $tag), $content);
        $content = str_replace('<schema_versions>', array_keys($versions), $content);
    }

    private function getMethodInfos(\ReflectionMethod $method): array
    {
        $name = $method->getName();
        $fn = [
            'filePath'  => $method->getDeclaringClass()->getFileName(),
            'startLine' => $method->getStartLine(),
            'endLine'   => $method->getEndLine(),
            'body'      => '',
        ];

        $fn['body'] = implode('', array_slice(
            file($fn['filePath']),
            $fn['startLine'],
            ($fn['endLine'] - $fn['startLine']) + 1
        ));

        return $fn;
    }
}
