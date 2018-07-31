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

class Version20180731100339 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE pia_processing (id INT NOT NULL, folder_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, author VARCHAR(255) NOT NULL, description TEXT NOT NULL, life_cycle_description TEXT NOT NULL, data_medium_description TEXT NOT NULL, standards_description TEXT NOT NULL, processors TEXT NOT NULL, controllers TEXT NOT NULL, data_transfer_outside_eu TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_81E5D0EC162CB942 ON pia_processing (folder_id)');
        $this->addSql('CREATE TABLE pia_processing_data_type (id INT NOT NULL, processing_id INT DEFAULT NULL, reference VARCHAR(255) NOT NULL, data VARCHAR(255) NOT NULL, retention_period VARCHAR(255) NOT NULL, sensitive BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A6855B535BAE24E8 ON pia_processing_data_type (processing_id)');
        $this->addSql('ALTER TABLE pia_processing ADD CONSTRAINT FK_81E5D0EC162CB942 FOREIGN KEY (folder_id) REFERENCES pia_folder (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pia_processing_data_type ADD CONSTRAINT FK_A6855B535BAE24E8 FOREIGN KEY (processing_id) REFERENCES pia_processing (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pia ADD processing_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE pia ADD CONSTRAINT FK_253A30625BAE24E8 FOREIGN KEY (processing_id) REFERENCES pia_processing (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_253A30625BAE24E8 ON pia (processing_id)');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE pia DROP CONSTRAINT FK_253A30625BAE24E8');
        $this->addSql('ALTER TABLE pia_processing_data_type DROP CONSTRAINT FK_A6855B535BAE24E8');
        $this->addSql('DROP TABLE pia_processing');
        $this->addSql('DROP TABLE pia_processing_data_type');
        $this->addSql('DROP INDEX IDX_253A30625BAE24E8');
        $this->addSql('ALTER TABLE pia DROP processing_id');
    }
}
