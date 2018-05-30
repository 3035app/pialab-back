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
final class Version20180530091757 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE pia_folder_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE pia_folder (id INT NOT NULL, name VARCHAR(255) NOT NULL, tree_root INT DEFAULT NULL, parent_id INT DEFAULT NULL, lft INT NOT NULL, lvl INT NOT NULL, rgt INT NOT NULL, structure_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_71BF4B04A977936C ON pia_folder (tree_root)');
        $this->addSql('CREATE INDEX IDX_71BF4B04727ACA70 ON pia_folder (parent_id)');
        $this->addSql('ALTER TABLE pia_folder ADD CONSTRAINT FK_71BF4B04A977936C FOREIGN KEY (tree_root) REFERENCES pia_folder (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pia_folder ADD CONSTRAINT FK_71BF4B04727ACA70 FOREIGN KEY (parent_id) REFERENCES pia_folder (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pia_folder ADD CONSTRAINT FK_71BF4B042534008B FOREIGN KEY (structure_id) REFERENCES pia_structure (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pia ADD folder_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE pia ADD CONSTRAINT FK_253A3062162CB942 FOREIGN KEY (folder_id) REFERENCES pia_folder (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_253A3062162CB942 ON pia (folder_id)');
        $this->addSql('CREATE INDEX IDX_71BF4B042534008B ON pia_folder (structure_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE pia DROP CONSTRAINT FK_253A3062162CB942');
        $this->addSql('ALTER TABLE pia_folder DROP CONSTRAINT FK_71BF4B042534008B');
        $this->addSql('ALTER TABLE pia_folder DROP CONSTRAINT FK_71BF4B04A977936C');
        $this->addSql('ALTER TABLE pia_folder DROP CONSTRAINT FK_71BF4B04727ACA70');
        $this->addSql('DROP SEQUENCE pia_folder_id_seq CASCADE');
        $this->addSql('DROP INDEX IDX_71BF4B042534008B');
        $this->addSql('DROP TABLE pia_folder');
        $this->addSql('DROP INDEX IDX_253A3062162CB942');
        $this->addSql('ALTER TABLE pia DROP folder_id');
    }
}
