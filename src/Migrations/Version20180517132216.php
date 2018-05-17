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

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180517132216 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        if ($schema->hasTable('pia_structure_type')) {
            return;
        }

        $this->addSql('CREATE SEQUENCE pia_structure_type_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE pia_structure_type (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE pia_structure ADD type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE pia_structure ADD CONSTRAINT FK_5036DBE6C54C8C93 FOREIGN KEY (type_id) REFERENCES pia_structure_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_5036DBE6C54C8C93 ON pia_structure (type_id)');
    }

    public function down(Schema $schema)
    {
        if (!$schema->hasTable('pia_structure_type')) {
            return;
        }

        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE pia_structure DROP CONSTRAINT FK_5036DBE6C54C8C93');
        $this->addSql('DROP SEQUENCE pia_structure_type_id_seq CASCADE');
        $this->addSql('DROP TABLE pia_structure_type');
        $this->addSql('DROP INDEX IDX_5036DBE6C54C8C93');
        $this->addSql('ALTER TABLE pia_structure DROP type_id');
    }
}
