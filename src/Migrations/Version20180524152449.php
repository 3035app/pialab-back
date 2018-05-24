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
final class Version20180524152449 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE pia_template_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE pia_template (id INT NOT NULL, enabled BOOLEAN NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NULL, data TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN pia_template.data IS \'(DC2Type:json_array)\'');
        $this->addSql('ALTER TABLE pia ADD template_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE pia ADD CONSTRAINT FK_253A30625DA0FB8 FOREIGN KEY (template_id) REFERENCES pia_template (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_253A30625DA0FB8 ON pia (template_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE pia DROP CONSTRAINT FK_253A30625DA0FB8');
        $this->addSql('DROP SEQUENCE pia_template_id_seq CASCADE');
        $this->addSql('DROP TABLE pia_template');
        $this->addSql('DROP INDEX IDX_253A30625DA0FB8');
        $this->addSql('ALTER TABLE pia DROP template_id');
    }
}
