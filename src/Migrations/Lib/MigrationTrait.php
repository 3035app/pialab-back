<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Migrations\Lib;

use Doctrine\DBAL\Schema\Schema;

trait MigrationTrait
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        foreach ($this->migrations['schema'] as $migration) {
            $this->executeVersion($migration, 'up', $schema);
        }
    }

    public function postUp(Schema $schema)
    {
        foreach ($this->migrations['data'] as $migration) {
            $this->executeVersion($migration, 'up', $schema);
        }
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        foreach (array_reverse($this->migrations['schema']) as $migration) {
            $this->executeVersion($migration, 'down', $schema);
        }
    }

    public function preDown(Schema $schema)
    {
        foreach (array_reverse($this->migrations['data']) as $migration) {
            $this->executeVersion($migration, 'down', $schema);
        }
    }

    /**
     * Executes specifyed version and update migration_versions table if necessary.
     *
     * @param string      $versionId
     * @param string|null $upOrDown
     *
     * @throws \InvalidArgumentException
     */
    public function executeVersion(string $versionId, ?string $upOrDown = 'up', Schema $schema): void
    {
        if ($upOrDown !== 'up' && $upOrDown !== 'down') {
            throw new \InvalidArgumentException(
                sprintf(
                    'Migration method « executeVersion » parameter $upOrDown must be « up » or « down ». %s given',
                    $upOrDown
                )
            );
        }

        if ($this->checkVersionAlreadyExists($versionId)) {
            $this->connection->executeQuery(sprintf('DELETE FROM migration_versions WHERE version = \'%s\'', $versionId));
        } else {
            $versionMethod = sprintf(
                'Version%s_%s',
                $versionId,
                $upOrDown
            );

            $this->{$versionMethod}($schema);
        }
    }

    public function cleanUpExistingMigrationsFoundInMigrationsTableAndSkip(): bool
    {
        $versionsInClause = '\'' . implode('\',\'', $this->migrations) . '\'';

        $existingVersions = $this->connection->executeQuery('SELECT version FROM migration_versions WHERE version IN (' . $versionsInClause . ')');

        if ($existingVersions->rowCount() === count($this->migrations)) {
            $this->addSql('DELETE FROM migration_versions WHERE version IN (' . $versionsInClause . ')');

            return true;
        }

        return false;
    }

    private function checkVersionAlreadyExists(string $versionId): bool
    {
        $existingVersion = $this->connection->executeQuery(sprintf('SELECT version FROM migration_versions WHERE version = \'%s\'', $versionId));

        return $existingVersion->rowCount() > 0;
    }
}
