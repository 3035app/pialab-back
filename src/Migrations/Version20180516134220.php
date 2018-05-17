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
class Version20180516134220 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        if ($schema->hasTable('pia_structure')) {
            return;
        }

        $this->addSql('CREATE SEQUENCE pia_structure_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE pia_structure (id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE pia_user ADD structure_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE pia_user ADD CONSTRAINT FK_260CA7F2534008B FOREIGN KEY (structure_id) REFERENCES pia_structure (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_260CA7F2534008B ON pia_user (structure_id)');
        $this->addSql('ALTER TABLE pia ADD structure_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE pia ADD CONSTRAINT FK_253A30622534008B FOREIGN KEY (structure_id) REFERENCES pia_structure (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_253A30622534008B ON pia (structure_id)');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        if (!$schema->hasTable('pia_structure')) {
            return;
        }

        $this->addSql('ALTER TABLE pia_user DROP CONSTRAINT FK_260CA7F2534008B');
        $this->addSql('ALTER TABLE pia DROP CONSTRAINT FK_253A30622534008B');
        $this->addSql('DROP SEQUENCE pia_structure_id_seq CASCADE');
        $this->addSql('DROP TABLE pia_structure');
        $this->addSql('DROP INDEX IDX_260CA7F2534008B');
        $this->addSql('ALTER TABLE pia_user DROP structure_id');
        $this->addSql('DROP INDEX IDX_253A30622534008B');
        $this->addSql('ALTER TABLE pia DROP structure_id');
    }
}
