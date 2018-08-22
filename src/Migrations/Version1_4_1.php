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

class Version1_4_1 extends AbstractMigration implements ContainerAwareInterface
{
    use
        ContainerAwareTrait,
        MigrationTrait
    ;

    private $migrations = [
        'schema' => [
            '20180731100339',
            '20180731132909',
            '20180731151014',
            '20180801083559',
            '20180801135832',
            '20180801152601',
            '20180803133256',
            '20180808143035',
        ],
        'data' => [
            // Please move here versions that uses directly doctrine entity manager
        ],
    ];

    // #########################################
    //         OLD VERSIONS BELOW
    // #########################################

    protected function Version20180731100339_up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        // $this->addSql('CREATE SEQUENCE pia_processing_id_seq INCREMENT BY 1 MINVALUE 1 START 1;');
        $this->addSql('CREATE SEQUENCE pia_processing_data_type_id_seq INCREMENT BY 1 MINVALUE 1 START 1;');
        $this->addSql('CREATE TABLE pia_processing (id INT NOT NULL, folder_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, author VARCHAR(255) NOT NULL, description TEXT NOT NULL, life_cycle_description TEXT NOT NULL, data_medium_description TEXT NOT NULL, standards_description TEXT NOT NULL, processors TEXT NOT NULL, controllers TEXT NOT NULL, data_transfer_outside_eu TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_81E5D0EC162CB942 ON pia_processing (folder_id)');
        $this->addSql('CREATE TABLE pia_processing_data_type (id INT NOT NULL, processing_id INT DEFAULT NULL, reference VARCHAR(255) NOT NULL, data TEXT NOT NULL, retention_period VARCHAR(255) NOT NULL, sensitive BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN pia_processing_data_type.data IS \'(DC2Type:json)\';');
        $this->addSql('CREATE INDEX IDX_A6855B535BAE24E8 ON pia_processing_data_type (processing_id)');
        $this->addSql('ALTER TABLE pia_processing ADD CONSTRAINT FK_81E5D0EC162CB942 FOREIGN KEY (folder_id) REFERENCES pia_folder (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pia_processing_data_type ADD CONSTRAINT FK_A6855B535BAE24E8 FOREIGN KEY (processing_id) REFERENCES pia_processing (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pia ADD processing_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE pia ADD CONSTRAINT FK_253A30625BAE24E8 FOREIGN KEY (processing_id) REFERENCES pia_processing (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_253A30625BAE24E8 ON pia (processing_id)');

        $this->movePiasIntoDedicatedProcessing();

        $this->addSql('ALTER TABLE pia DROP CONSTRAINT fk_253a3062162cb942;');
        $this->addSql('DROP INDEX idx_253a3062162cb942;');
        $this->addSql('ALTER TABLE pia DROP folder_id;');
    }

    protected function Version20180731100339_down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE pia DROP CONSTRAINT FK_253A30625BAE24E8');
        $this->addSql('ALTER TABLE pia_processing_data_type DROP CONSTRAINT FK_A6855B535BAE24E8');
        $this->addSql('DROP TABLE pia_processing');
        $this->addSql('DROP TABLE pia_processing_data_type');
        $this->addSql('DROP INDEX IDX_253A30625BAE24E8');
        $this->addSql('ALTER TABLE pia DROP processing_id');
        $this->addSql('DROP SEQUENCE pia_processing_id_seq');
        $this->addSql('DROP SEQUENCE pia_processing_data_type_id_seq');

        $this->addSql('ALTER TABLE pia ADD folder_id INT EFAULT NULL;');
        $this->addSql('ALTER TABLE pia ADD CONSTRAINT fk_253a3062162cb942 FOREIGN KEY (folder_id) REFERENCES pia_folder (id) NOT DEFERRABLE INITIALLY IMMEDIATE;');
        $this->addSql('CREATE INDEX idx_253a3062162cb942 ON pia (folder_id);');
    }

    protected function Version20180731132909_up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE pia_processing ADD life_cycle TEXT NULL');
        $this->addSql('ALTER TABLE pia_processing ADD storage TEXT NULL');
        $this->addSql('ALTER TABLE pia_processing ADD standards TEXT NULL');
        $this->addSql('ALTER TABLE pia_processing DROP life_cycle_description');
        $this->addSql('ALTER TABLE pia_processing DROP data_medium_description');
        $this->addSql('ALTER TABLE pia_processing DROP standards_description');
        $this->addSql('ALTER TABLE pia_processing RENAME COLUMN data_transfer_outside_eu TO non_eu_transfer');
    }

    protected function Version20180731132909_down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE pia_processing ADD life_cycle_description TEXT NOT NULL');
        $this->addSql('ALTER TABLE pia_processing ADD data_medium_description TEXT NOT NULL');
        $this->addSql('ALTER TABLE pia_processing ADD standards_description TEXT NOT NULL');
        $this->addSql('ALTER TABLE pia_processing DROP life_cycle');
        $this->addSql('ALTER TABLE pia_processing DROP storage');
        $this->addSql('ALTER TABLE pia_processing DROP standards');
        $this->addSql('ALTER TABLE pia_processing RENAME COLUMN non_eu_transfer TO data_transfer_outside_eu');
    }

    protected function Version20180731151014_up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE pia_processing ADD status INT NOT NULL DEFAULT 0');
    }

    protected function Version20180731151014_down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE pia_processing DROP status');
    }

    protected function Version20180801083559_up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE pia_processing ALTER description DROP NOT NULL');
    }

    protected function Version20180801083559_down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE pia_processing ALTER description SET NOT NULL');
        $this->addSql('ALTER TABLE pia_processing ALTER life_cycle SET NOT NULL');
        $this->addSql('ALTER TABLE pia_processing ALTER storage SET NOT NULL');
        $this->addSql('ALTER TABLE pia_processing ALTER standards SET NOT NULL');
    }

    protected function Version20180801135832_up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE pia_processing ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NULL');
        $this->addSql('ALTER TABLE pia_processing ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE NULL');
        $this->addSql('UPDATE pia_processing SET created_at = NOW(), updated_at = NOW()');
        $this->addSql('ALTER TABLE pia_processing ALTER COLUMN created_at SET NOT NULL');
        $this->addSql('ALTER TABLE pia_processing ALTER COLUMN updated_at SET NOT NULL');
    }

    protected function Version20180801135832_down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE pia_processing DROP created_at');
        $this->addSql('ALTER TABLE pia_processing DROP updated_at');
    }

    protected function Version20180801152601_up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE pia_processing ALTER processors DROP NOT NULL');
    }

    protected function Version20180801152601_down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE pia_processing ALTER processors SET NOT NULL');
    }

    protected function Version20180803133256_up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE pia_processing_data_type ALTER data DROP NOT NULL');
        $this->addSql('ALTER TABLE pia_processing_data_type ALTER retention_period DROP NOT NULL');
    }

    protected function Version20180803133256_down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE pia_processing_data_type ALTER data SET NOT NULL');
        $this->addSql('ALTER TABLE pia_processing_data_type ALTER retention_period SET NOT NULL');
    }

    protected function Version20180808143035_up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE pia_processing_data_type ALTER data TYPE TEXT');
        $this->addSql('ALTER TABLE pia_processing_data_type ALTER data DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN pia_processing_data_type.data IS NULL;');
    }

    protected function Version20180808143035_down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE pia_processing_data_type ALTER data TYPE TEXT');
        $this->addSql('ALTER TABLE pia_processing_data_type ALTER data DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN pia_processing_data_type.data IS \'(DC2Type:json)\';');
    }

    protected function movePiasIntoDedicatedProcessing(): void
    {
        $this->connection->executeQuery('CREATE SEQUENCE pia_processing_id_seq INCREMENT BY 1 MINVALUE 1 START 1;');

        $rsm = $this->connection->executeQuery('SELECT * FROM pia WHERE folder_id IS NOT NULL');

        foreach ($rsm->fetchAll() as $data) {
            // For each PIA we create a Processing with basic informations and put it into the same folder.

            $processingId = $this->connection->executeQuery('SELECT nextval(\'pia_processing_id_seq\')')->fetchColumn(0);

            // Creating the processing

            $this->addSql(
                sprintf(
                    'INSERT INTO pia_processing (
                        id,name,description,author,controllers,folder_id,life_cycle_description,data_medium_description,standards_description,processors
                    ) VALUES (
                        %d,\'%s\',\'%s\',\'%s\',\'%s\',%d,\'%s\',\'%s\',\'%s\',\'%s\'
                    )',
                    $processingId,
                    $this->escapeString($data['name']),
                    'migrated Processing from existing PIA',
                    $this->escapeString($data['author_name']),
                    'TBD',
                    $data['folder_id'],
                    'N/A',
                    'N/A',
                    'N/A',
                    'TBD'
                )
            );

            // Updating the pia

            $this->addSql(
                sprintf(
                    'UPDATE pia SET processing_id = %d WHERE id = %d',
                    $processingId,
                    $data['id']
                )
            );
        }
    }

    private function escapeString(string $string): string
    {
        return preg_replace('/\'/', '\'\'', $string);
    }
}
