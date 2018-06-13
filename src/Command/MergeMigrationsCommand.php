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
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;

class MergeMigrationsCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var string
     */
    private $doctrineMigrationPath;

    /**
     * @var string
     */
    private $doctrineMigrationNamespace;

    /**
     * @var string
     */
    private $majorMigrationSkeletonName = 'majorMigration.skeleton';

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
doctrine:migrations:diff or doctrine:migrations:generate commands to one corresponding to a specific tag name.

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

        $tag = $input->getArgument('tag');

        $versions = $this->handlingExistingMigrations();

        $this->createTargetMigration($tag, $versions);

        return 0;
    }

    /**
     * Init parameters and loads migration classes.
     */
    private function init()
    {
        $this->doctrineMigrationPath = $this->container->getParameter('doctrine_migrations.dir_name');
        $this->doctrineMigrationNamespace = $this->container->getParameter('doctrine_migrations.namespace');

        $finder = new Finder();
        $finder->files()->in($this->doctrineMigrationPath)->name('/^Version[0-9]{14}\.php/');

        // Loading migrations classes

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            include $file->getRealPath();
        }
    }

    /**
     * Undocumented function.
     *
     * @return array
     */
    private function handlingExistingMigrations(): array
    {
        // Retrieves migrations from namespaces

        $doctrineMigrationNamespace = $this->doctrineMigrationNamespace;
        $migrations = array_filter(get_declared_classes(), function ($class) use ($doctrineMigrationNamespace) {
            preg_match('/^' . $doctrineMigrationNamespace . '\\\/', $class, $found);

            return count($found) > 0;
        });

        // Looping through versions to build methods definitions

        $versions = [];

        foreach ($migrations as $migrationClass) {
            $reflectionClass = new \ReflectionClass($migrationClass);

            $versionCode = str_replace('Version', '', $reflectionClass->getShortName());

            $fn = [
                'up'   => null,
                'down' => null,
            ];

            // Handling up method

            $up = $reflectionClass->getMethod('up');

            $fn['up'] = [
                'filePath'  => $reflectionClass->getFileName(),
                'startLine' => $up->getStartLine(),
                'endLine'   => $up->getEndLine(),
                'body'      => '',
            ];

            $fn['up']['body'] = implode('', array_slice(file($fn['up']['filePath']), $fn['up']['startLine'], ($fn['up']['endLine'] - $fn['up']['startLine']) + 1));

            // Handling down method

            $down = $reflectionClass->getMethod('down');

            $fn['down'] = $this->getMethodInfos($down);

            $versions[$versionCode] = $fn;
        }

        ksort($versions);

        return $versions;
    }

    private function createTargetMigration(string $tag, array $versions): void
    {
        $migrationSkeleton = $this->doctrineMigrationPath . '/Lib/' . $this->majorMigrationSkeletonName;
        $versionTag = str_replace('.', '_', $tag);

        $content = file_get_contents($migrationSkeleton);

        $content = str_replace('<version_tag>', $versionTag, $content);
        $content = str_replace('<schema_versions>', '\'' . implode('\',' . "\n            " . '\'', array_keys($versions)) . '\',', $content);

        $oldVersions = '';

        foreach ($versions as $version => $methods) {
            foreach ($methods as $methodName => $methodInfos) {
                $methodName = sprintf('    protected function Version%s_%s(Schema $schema): void', $version, $methodName);
                $oldVersions .= "\n" . $methodName . "\n" . $methodInfos['body'];
            }
        }

        $content = str_replace('<old_versions>', $oldVersions, $content);

        $fs = new Filesystem();

        $majorMigrationFilename = $this->doctrineMigrationPath . '/Version' . $versionTag . '.php';

        $fs->touch($majorMigrationFilename);
        $fs->appendToFile($majorMigrationFilename, $content);

        foreach (array_keys($versions) as $version) {
            $fs->remove($this->doctrineMigrationPath . '/Version' . $version . '.php');
        }
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
            ($fn['endLine'] - $fn['startLine'])
        ));

        return $fn;
    }
}
