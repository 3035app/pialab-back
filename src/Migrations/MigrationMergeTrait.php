<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Migrations;

trait MigrationMergeTrait
{
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
}
