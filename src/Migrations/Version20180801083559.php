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

class Version20180801083559 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE pia_processing ALTER description DROP NOT NULL');
        $this->addSql('ALTER TABLE pia_processing ALTER life_cycle DROP NOT NULL');
        $this->addSql('ALTER TABLE pia_processing ALTER storage DROP NOT NULL');
        $this->addSql('ALTER TABLE pia_processing ALTER standards DROP NOT NULL');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE pia_processing ALTER description SET NOT NULL');
        $this->addSql('ALTER TABLE pia_processing ALTER life_cycle SET NOT NULL');
        $this->addSql('ALTER TABLE pia_processing ALTER storage SET NOT NULL');
        $this->addSql('ALTER TABLE pia_processing ALTER standards SET NOT NULL');
    }
}
