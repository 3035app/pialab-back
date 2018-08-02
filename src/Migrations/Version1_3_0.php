<?php

declare(strict_types=1);

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
use PiaApi\Migrations\Lib\MigrationTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version1_3_0 extends AbstractMigration implements ContainerAwareInterface
{
    use
        ContainerAwareTrait,
        MigrationTrait
    ;

    private $migrations = [
        'schema' => [
            '20180709214600',
            '20180710103217',
        ],
        'data' => [
            // Please move here versions that uses directly doctrine entity manager
        ],
    ];

    // #########################################
    //         OLD VERSIONS BELOW
    // #########################################

    protected function Version20180709214600_up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE pia_portfolio_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE pia_portfolio (id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE pia_structure ADD portfolio_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE pia_structure ADD CONSTRAINT FK_5036DBE6B96B5643 FOREIGN KEY (portfolio_id) REFERENCES pia_portfolio (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_5036DBE6B96B5643 ON pia_structure (portfolio_id)');
    }

    protected function Version20180709214600_down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE pia_structure DROP CONSTRAINT FK_5036DBE6B96B5643');
        $this->addSql('DROP SEQUENCE pia_portfolio_id_seq CASCADE');
        $this->addSql('DROP TABLE pia_portfolio');
        $this->addSql('DROP INDEX IDX_5036DBE6B96B5643');
        $this->addSql('ALTER TABLE pia_structure DROP portfolio_id');
    }

    protected function Version20180710103217_up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE pia_users__portfolios (user_id INT NOT NULL, user_portfolio_id INT NOT NULL, PRIMARY KEY(user_id, user_portfolio_id))');
        $this->addSql('CREATE INDEX IDX_85442F8BA76ED395 ON pia_users__portfolios (user_id)');
        $this->addSql('CREATE INDEX IDX_85442F8B39A72A41 ON pia_users__portfolios (user_portfolio_id)');
        $this->addSql('ALTER TABLE pia_users__portfolios ADD CONSTRAINT FK_85442F8BA76ED395 FOREIGN KEY (user_id) REFERENCES pia_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pia_users__portfolios ADD CONSTRAINT FK_85442F8B39A72A41 FOREIGN KEY (user_portfolio_id) REFERENCES pia_portfolio (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    protected function Version20180710103217_down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE pia_users__portfolios');
    }
}
