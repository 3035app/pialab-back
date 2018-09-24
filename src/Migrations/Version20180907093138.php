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

class Version20180907093138 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE pia_processing ADD designated_controller TEXT');
        $this->addSql('UPDATE pia_processing set designated_controller = controllers WHERE controllers IS NOT NULL');
        $this->addSql('ALTER TABLE pia_processing ALTER designated_controller SET NOT NULL');
        $this->addSql('ALTER TABLE pia_processing ALTER controllers DROP NOT NULL');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('UPDATE pia_processing set controllers = designated_controller WHERE designated_controller IS NOT NULL');
        $this->addSql('ALTER TABLE pia_processing DROP designated_controller');
        $this->addSql('ALTER TABLE pia_processing ALTER controllers SET NOT NULL');
    }
}
