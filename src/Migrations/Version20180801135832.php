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

class Version20180801135832 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE pia_processing ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NULL');
        $this->addSql('ALTER TABLE pia_processing ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE NULL');
        $this->addSql('UPDATE pia_processing SET created_at = NOW(), updated_at = NOW()');
        $this->addSql('ALTER TABLE pia_processing ALTER COLUMN created_at SET NOT NULL');
        $this->addSql('ALTER TABLE pia_processing ALTER COLUMN updated_at SET NOT NULL');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE pia_processing DROP created_at');
        $this->addSql('ALTER TABLE pia_processing DROP updated_at');
    }
}
