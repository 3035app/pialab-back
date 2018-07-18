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

class Version20180710103217 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE pia_users__portfolios (user_id INT NOT NULL, user_portfolio_id INT NOT NULL, PRIMARY KEY(user_id, user_portfolio_id))');
        $this->addSql('CREATE INDEX IDX_85442F8BA76ED395 ON pia_users__portfolios (user_id)');
        $this->addSql('CREATE INDEX IDX_85442F8B39A72A41 ON pia_users__portfolios (user_portfolio_id)');
        $this->addSql('ALTER TABLE pia_users__portfolios ADD CONSTRAINT FK_85442F8BA76ED395 FOREIGN KEY (user_id) REFERENCES pia_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pia_users__portfolios ADD CONSTRAINT FK_85442F8B39A72A41 FOREIGN KEY (user_portfolio_id) REFERENCES pia_portfolio (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE pia_users__portfolios');
    }
}
