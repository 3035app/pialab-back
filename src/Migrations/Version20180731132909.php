<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20180731132909 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE pia_processing ADD life_cycle TEXT NOT NULL');
        $this->addSql('ALTER TABLE pia_processing ADD storage TEXT NOT NULL');
        $this->addSql('ALTER TABLE pia_processing ADD standards TEXT NOT NULL');
        $this->addSql('ALTER TABLE pia_processing DROP life_cycle_description');
        $this->addSql('ALTER TABLE pia_processing DROP data_medium_description');
        $this->addSql('ALTER TABLE pia_processing DROP standards_description');
        $this->addSql('ALTER TABLE pia_processing RENAME COLUMN data_transfer_outside_eu TO non_eu_transfer');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE pia_processing ADD life_cycle_description TEXT NOT NULL');
        $this->addSql('ALTER TABLE pia_processing ADD data_medium_description TEXT NOT NULL');
        $this->addSql('ALTER TABLE pia_processing ADD standards_description TEXT NOT NULL');
        $this->addSql('ALTER TABLE pia_processing DROP life_cycle');
        $this->addSql('ALTER TABLE pia_processing DROP storage');
        $this->addSql('ALTER TABLE pia_processing DROP standards');
        $this->addSql('ALTER TABLE pia_processing RENAME COLUMN non_eu_transfer TO data_transfer_outside_eu');
    }
}
