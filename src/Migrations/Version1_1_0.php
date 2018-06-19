<?php

declare(strict_types=1);

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use PiaApi\Migrations\Lib\MigrationTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version1_1_0 extends AbstractMigration implements ContainerAwareInterface
{
    use
        ContainerAwareTrait,
        MigrationTrait
    ;

    private $migrations = [
        'schema' => [
            '20180612141711',
        ],
        'data' => [
            // Please move here versions that uses directly doctrine entity manager
        ],
    ];

    // #########################################
    //         OLD VERSIONS BELOW
    // #########################################


    protected function Version20180612141711_up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE pia ADD type VARCHAR(255) NULL');
        $this->addSql("UPDATE pia set type='advanced'");
        $this->addSql('ALTER TABLE pia ALTER COLUMN type VARCHAR(255) NOT NULL');
    }

    protected function Version20180612141711_down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE pia DROP type');
    }

}
