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
final class Version20180524074718 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE pia_profile ADD last_name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE pia_profile DROP pia_roles');
        $this->addSql('ALTER TABLE pia_profile RENAME COLUMN name TO first_name');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE pia_profile ADD name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE pia_profile ADD pia_roles TEXT NOT NULL');
        $this->addSql('ALTER TABLE pia_profile DROP first_name');
        $this->addSql('ALTER TABLE pia_profile DROP last_name');
        $this->addSql('COMMENT ON COLUMN pia_profile.pia_roles IS \'(DC2Type:json)\'');
    }
}
