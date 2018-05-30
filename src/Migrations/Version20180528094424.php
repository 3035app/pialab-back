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
final class Version20180528094424 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE pia_templates__structures (structure_id INT NOT NULL, structure_pia_template_id INT NOT NULL, PRIMARY KEY(structure_id, structure_pia_template_id))');
        $this->addSql('CREATE INDEX IDX_56DAC3242534008B ON pia_templates__structures (structure_id)');
        $this->addSql('CREATE INDEX IDX_56DAC324F2DACB5 ON pia_templates__structures (structure_pia_template_id)');
        $this->addSql('CREATE TABLE pia_templates__structure_types (structure_type_id INT NOT NULL, structure_type_pia_template_id INT NOT NULL, PRIMARY KEY(structure_type_id, structure_type_pia_template_id))');
        $this->addSql('CREATE INDEX IDX_1518CE651EEEFCA2 ON pia_templates__structure_types (structure_type_id)');
        $this->addSql('CREATE INDEX IDX_1518CE657A287CD ON pia_templates__structure_types (structure_type_pia_template_id)');
        $this->addSql('ALTER TABLE pia_templates__structures ADD CONSTRAINT FK_56DAC3242534008B FOREIGN KEY (structure_id) REFERENCES pia_template (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pia_templates__structures ADD CONSTRAINT FK_56DAC324F2DACB5 FOREIGN KEY (structure_pia_template_id) REFERENCES pia_structure (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pia_templates__structure_types ADD CONSTRAINT FK_1518CE651EEEFCA2 FOREIGN KEY (structure_type_id) REFERENCES pia_template (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pia_templates__structure_types ADD CONSTRAINT FK_1518CE657A287CD FOREIGN KEY (structure_type_pia_template_id) REFERENCES pia_structure_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE pia_templates__structures');
        $this->addSql('DROP TABLE pia_templates__structure_types');
    }
}
