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

class Version20180709214600 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE pia_portfolio_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE pia_portfolio (id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE pia_structure ADD portfolio_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE pia_structure ADD CONSTRAINT FK_5036DBE6B96B5643 FOREIGN KEY (portfolio_id) REFERENCES pia_portfolio (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_5036DBE6B96B5643 ON pia_structure (portfolio_id)');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE pia_structure DROP CONSTRAINT FK_5036DBE6B96B5643');
        $this->addSql('DROP SEQUENCE pia_portfolio_id_seq CASCADE');
        $this->addSql('DROP TABLE pia_portfolio');
        $this->addSql('DROP INDEX IDX_5036DBE6B96B5643');
        $this->addSql('ALTER TABLE pia_structure DROP portfolio_id');
    }
}
