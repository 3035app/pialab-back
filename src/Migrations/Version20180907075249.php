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

class Version20180907075249 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE pia DROP CONSTRAINT fk_253a30625da0fb8');
        $this->addSql('DROP INDEX idx_253a30625da0fb8');
        $this->addSql('ALTER TABLE pia DROP template_id');
        $this->addSql('ALTER TABLE pia_processing ADD template_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE pia_processing ADD CONSTRAINT FK_81E5D0EC5DA0FB8 FOREIGN KEY (template_id) REFERENCES pia_template (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_81E5D0EC5DA0FB8 ON pia_processing (template_id)');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE pia_processing DROP CONSTRAINT FK_81E5D0EC5DA0FB8');
        $this->addSql('DROP INDEX IDX_81E5D0EC5DA0FB8');
        $this->addSql('ALTER TABLE pia_processing DROP template_id');
        $this->addSql('ALTER TABLE pia ADD template_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE pia ADD CONSTRAINT fk_253a30625da0fb8 FOREIGN KEY (template_id) REFERENCES pia_template (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_253a30625da0fb8 ON pia (template_id)');
    }
}
