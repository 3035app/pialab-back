<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20181010095819 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE pia_structure ADD executive VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE pia_structure ADD backup VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE pia_structure ADD dpo VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE pia_structure DROP executive');
        $this->addSql('ALTER TABLE pia_structure DROP backup');
        $this->addSql('ALTER TABLE pia_structure DROP dpo');
    }
}