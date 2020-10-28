<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20180917124818 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE pia_processing ADD lawfulness TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE pia_processing ADD minimization TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE pia_processing ADD rights_guarantee TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE pia_processing ADD exactness TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE pia_processing ADD consent TEXT DEFAULT NULL');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE pia_processing DROP lawfulness');
        $this->addSql('ALTER TABLE pia_processing DROP minimization');
        $this->addSql('ALTER TABLE pia_processing DROP rights_guarantee');
        $this->addSql('ALTER TABLE pia_processing DROP exactness');
        $this->addSql('ALTER TABLE pia_processing DROP consent');
    }
}