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

class Version20180912084122 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE pia_structure ADD address VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE pia_structure ADD phone VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE pia_structure ADD siren VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE pia_structure ADD siret VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE pia_structure ADD vat_number VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE pia_structure ADD activity_code VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE pia_structure ADD legal_form VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE pia_structure ADD registration_date VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE pia_structure DROP address');
        $this->addSql('ALTER TABLE pia_structure DROP phone');
        $this->addSql('ALTER TABLE pia_structure DROP siren');
        $this->addSql('ALTER TABLE pia_structure DROP siret');
        $this->addSql('ALTER TABLE pia_structure DROP vat_number');
        $this->addSql('ALTER TABLE pia_structure DROP activity_code');
        $this->addSql('ALTER TABLE pia_structure DROP legal_form');
        $this->addSql('ALTER TABLE pia_structure DROP registration_date');
    }
}
