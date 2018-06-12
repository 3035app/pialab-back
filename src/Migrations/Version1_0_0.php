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
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use PiaApi\Entity\Pia\Structure;
use PiaApi\Entity\Pia\Folder;
use Symfony\Bridge\Doctrine\RegistryInterface;
use PiaApi\Migrations\MigrationMergeTrait;

class Version1_0_0 extends AbstractMigration implements ContainerAwareInterface
{
    use MigrationMergeTrait;

    /**
     * @var ContainerInterface
     */
    private $container;

    private $migrations = [
        '00000000000000',
        '20180515154624',
        '20180516134220',
        '20180517132216',
        '20180522073548',
        '20180524073608',
        '20180524074718',
        '20180524080353',
        '20180524100033',
        '20180524152449',
        '20180528094424',
        '20180528125823',
        '20180528142132',
        '20180530091757',
        '20180530095437',
        '20180605082149',
    ];

    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        // if ($this->cleanUpExistingMigrationsFoundInMigrationsTableAndSkip()) {
        //     return;
        // }

        // @TODO (For all Versions): Create a function in MigrationMergeTrait that do :
        //        - Check if old migration_version exists.
        //        - If exists, delete from migration_versions and skip this Version.
        //        - Else, execute this Version

        // @TODO: Move to Version1_0_0_data.php Versions 20180530095437 and 20180605082149 that uses Doctrine EM
        //        Because of SQL transaction that changes are not visible by doctrine when used inside the same migration

        $this->Version00000000000000_up();

        $this->Version20180515154624_up();

        $this->Version20180516134220_up();

        $this->Version20180517132216_up();

        $this->Version20180522073548_up();

        $this->Version20180524073608_up();

        $this->Version20180524074718_up();

        $this->Version20180524080353_up();

        $this->Version20180524100033_up();

        $this->Version20180524152449_up();

        $this->Version20180528094424_up();

        $this->Version20180528125823_up();

        $this->Version20180528142132_up();

        $this->Version20180530091757_up();

        $this->Version20180530095437_up();

        $this->Version20180605082149_up();
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->Version20180605082149_down();

        $this->Version20180530095437_down();

        $this->Version20180530091757_down();

        $this->Version20180528142132_down();

        $this->Version20180528125823_down();

        $this->Version20180528094424_down();

        $this->Version20180524152449_down();

        $this->Version20180524100033_down();

        $this->Version20180524080353_down();

        $this->Version20180524074718_down();

        $this->Version20180524073608_down();

        $this->Version20180522073548_down();

        $this->Version20180517132216_down();

        $this->Version20180516134220_down();

        $this->Version20180515154624_down();

        $this->Version00000000000000_down();
    }

    protected function Version00000000000000_up(): void
    {
        $this->addSql('CREATE SEQUENCE oauth_access_token_id_seq INCREMENT BY 1 MINVALUE 1 START 1;');
        $this->addSql('CREATE SEQUENCE oauth_client_id_seq INCREMENT BY 1 MINVALUE 1 START 1;');
        $this->addSql('CREATE SEQUENCE pia_user_id_seq INCREMENT BY 1 MINVALUE 1 START 1;');
        $this->addSql('CREATE SEQUENCE oauth_refresh_token_id_seq INCREMENT BY 1 MINVALUE 1 START 1;');
        $this->addSql('CREATE SEQUENCE oauth_auth_code_id_seq INCREMENT BY 1 MINVALUE 1 START 1;');
        $this->addSql('CREATE TABLE oauth_access_token (id INT NOT NULL, client_id INT NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) NOT NULL, expires_at INT DEFAULT NULL, scope VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id));');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F7FA86A45F37A13B ON oauth_access_token (token);');
        $this->addSql('CREATE INDEX IDX_F7FA86A419EB6921 ON oauth_access_token (client_id);');
        $this->addSql('CREATE INDEX IDX_F7FA86A4A76ED395 ON oauth_access_token (user_id);');
        $this->addSql('CREATE TABLE oauth_client (id INT NOT NULL, random_id VARCHAR(255) NOT NULL, redirect_uris TEXT NOT NULL, secret VARCHAR(255) NOT NULL, allowed_grant_types TEXT NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id));');
        $this->addSql('COMMENT ON COLUMN oauth_client.redirect_uris IS \'(DC2Type:array)\';');
        $this->addSql('COMMENT ON COLUMN oauth_client.allowed_grant_types IS \'(DC2Type:array)\';');
        $this->addSql('CREATE TABLE pia_user (id INT NOT NULL, application_id INT DEFAULT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles TEXT NOT NULL, creationDate TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expirationDate TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, enabled BOOLEAN NOT NULL, locked BOOLEAN NOT NULL, PRIMARY KEY(id));');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_260CA7FF85E0677 ON pia_user (username);');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_260CA7FE7927C74 ON pia_user (email);');
        $this->addSql('CREATE INDEX IDX_260CA7F3E030ACD ON pia_user (application_id);');
        $this->addSql('COMMENT ON COLUMN pia_user.roles IS \'(DC2Type:array)\';');
        $this->addSql('CREATE TABLE oauth_refresh_token (id INT NOT NULL, client_id INT NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) NOT NULL, expires_at INT DEFAULT NULL, scope VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id));');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_55DCF7555F37A13B ON oauth_refresh_token (token);');
        $this->addSql('CREATE INDEX IDX_55DCF75519EB6921 ON oauth_refresh_token (client_id);');
        $this->addSql('CREATE INDEX IDX_55DCF755A76ED395 ON oauth_refresh_token (user_id);');
        $this->addSql('CREATE TABLE oauth_auth_code (id INT NOT NULL, client_id INT NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) NOT NULL, redirect_uri TEXT NOT NULL, expires_at INT DEFAULT NULL, scope VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id));');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4D12F0E05F37A13B ON oauth_auth_code (token);');
        $this->addSql('CREATE INDEX IDX_4D12F0E019EB6921 ON oauth_auth_code (client_id);');
        $this->addSql('CREATE INDEX IDX_4D12F0E0A76ED395 ON oauth_auth_code (user_id);');
        $this->addSql('ALTER TABLE oauth_access_token ADD CONSTRAINT FK_F7FA86A419EB6921 FOREIGN KEY (client_id) REFERENCES oauth_client (id) NOT DEFERRABLE INITIALLY IMMEDIATE;');
        $this->addSql('ALTER TABLE oauth_access_token ADD CONSTRAINT FK_F7FA86A4A76ED395 FOREIGN KEY (user_id) REFERENCES pia_user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;');
        $this->addSql('ALTER TABLE pia_user ADD CONSTRAINT FK_260CA7F3E030ACD FOREIGN KEY (application_id) REFERENCES oauth_client (id) NOT DEFERRABLE INITIALLY IMMEDIATE;');
        $this->addSql('ALTER TABLE oauth_refresh_token ADD CONSTRAINT FK_55DCF75519EB6921 FOREIGN KEY (client_id) REFERENCES oauth_client (id) NOT DEFERRABLE INITIALLY IMMEDIATE;');
        $this->addSql('ALTER TABLE oauth_refresh_token ADD CONSTRAINT FK_55DCF755A76ED395 FOREIGN KEY (user_id) REFERENCES pia_user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;');
        $this->addSql('ALTER TABLE oauth_auth_code ADD CONSTRAINT FK_4D12F0E019EB6921 FOREIGN KEY (client_id) REFERENCES oauth_client (id) NOT DEFERRABLE INITIALLY IMMEDIATE;');
        $this->addSql('ALTER TABLE oauth_auth_code ADD CONSTRAINT FK_4D12F0E0A76ED395 FOREIGN KEY (user_id) REFERENCES pia_user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;');

        // PIAs

        $this->addSql('CREATE SEQUENCE pia_comment_id_seq INCREMENT BY 1 MINVALUE 1 START 1;');
        $this->addSql('CREATE SEQUENCE pia_attachment_id_seq INCREMENT BY 1 MINVALUE 1 START 1;');
        $this->addSql('CREATE SEQUENCE pia_measure_id_seq INCREMENT BY 1 MINVALUE 1 START 1;');
        $this->addSql('CREATE SEQUENCE pia_id_seq INCREMENT BY 1 MINVALUE 1 START 1;');
        $this->addSql('CREATE SEQUENCE pia_answer_id_seq INCREMENT BY 1 MINVALUE 1 START 1;');
        $this->addSql('CREATE SEQUENCE pia_evaluation_id_seq INCREMENT BY 1 MINVALUE 1 START 1;');
        $this->addSql('CREATE TABLE pia_comment (id INT NOT NULL, pia_id INT DEFAULT NULL, description TEXT NOT NULL, reference_to VARCHAR(255) NOT NULL, for_measure BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id));');
        $this->addSql('CREATE INDEX IDX_7651353A3458351A ON pia_comment (pia_id);');
        $this->addSql('CREATE TABLE pia_attachment (id INT NOT NULL, pia_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, mime_type VARCHAR(255) NOT NULL, attachment_file TEXT NOT NULL, pia_signed BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id));');
        $this->addSql('CREATE INDEX IDX_70D6A27C3458351A ON pia_attachment (pia_id);');
        $this->addSql('CREATE TABLE pia_measure (id INT NOT NULL, pia_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, content TEXT NOT NULL, placeholder VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id));');
        $this->addSql('CREATE INDEX IDX_62227E733458351A ON pia_measure (pia_id);');
        $this->addSql('CREATE TABLE pia (id INT NOT NULL, status SMALLINT NOT NULL, name VARCHAR(255) NOT NULL, author_name VARCHAR(255) NOT NULL, evaluator_name VARCHAR(255) NOT NULL, validator_name VARCHAR(255) NOT NULL, dpo_status SMALLINT NOT NULL, dpo_opinion TEXT DEFAULT NULL, concerned_people_opinion TEXT DEFAULT NULL, concerned_people_status SMALLINT NOT NULL, concerned_people_searched_opinion BOOLEAN DEFAULT NULL, concerned_people_searched_content TEXT DEFAULT NULL, rejection_reason TEXT DEFAULT NULL, applied_adjustements TEXT DEFAULT NULL, dpos_names VARCHAR(255) NOT NULL, people_names VARCHAR(255) DEFAULT NULL, is_example BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id));');
        $this->addSql('CREATE TABLE pia_answer (id INT NOT NULL, pia_id INT DEFAULT NULL, reference_to VARCHAR(255) NOT NULL, data TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id));');
        $this->addSql('CREATE INDEX IDX_47C008EC3458351A ON pia_answer (pia_id);');
        $this->addSql('COMMENT ON COLUMN pia_answer.data IS \'(DC2Type:json)\';');
        $this->addSql('CREATE TABLE pia_evaluation (id INT NOT NULL, pia_id INT DEFAULT NULL, status SMALLINT NOT NULL, reference_to VARCHAR(255) NOT NULL, action_plan_comment TEXT DEFAULT NULL, evaluation_comment TEXT DEFAULT NULL, evaluation_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, gauges TEXT NOT NULL, estimated_implementation_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, person_in_charge VARCHAR(255) DEFAULT NULL, global_status SMALLINT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id));');
        $this->addSql('CREATE INDEX IDX_1AAADEB23458351A ON pia_evaluation (pia_id);');
        $this->addSql('COMMENT ON COLUMN pia_evaluation.gauges IS \'(DC2Type:json)\';');
        $this->addSql('ALTER TABLE pia_comment ADD CONSTRAINT FK_7651353A3458351A FOREIGN KEY (pia_id) REFERENCES pia (id) NOT DEFERRABLE INITIALLY IMMEDIATE;');
        $this->addSql('ALTER TABLE pia_attachment ADD CONSTRAINT FK_70D6A27C3458351A FOREIGN KEY (pia_id) REFERENCES pia (id) NOT DEFERRABLE INITIALLY IMMEDIATE;');
        $this->addSql('ALTER TABLE pia_measure ADD CONSTRAINT FK_62227E733458351A FOREIGN KEY (pia_id) REFERENCES pia (id) NOT DEFERRABLE INITIALLY IMMEDIATE;');
        $this->addSql('ALTER TABLE pia_answer ADD CONSTRAINT FK_47C008EC3458351A FOREIGN KEY (pia_id) REFERENCES pia (id) NOT DEFERRABLE INITIALLY IMMEDIATE;');
        $this->addSql('ALTER TABLE pia_evaluation ADD CONSTRAINT FK_1AAADEB23458351A FOREIGN KEY (pia_id) REFERENCES pia (id) NOT DEFERRABLE INITIALLY IMMEDIATE;');
    }

    protected function Version00000000000000_down(): void
    {
        $this->addSql('DROP SCHEMA public CASCADE;');
        $this->addSql('CREATE SCHEMA public;');
    }

    protected function Version20180515154624_up(): void
    {
        $this->addSql('ALTER TABLE oauth_client ADD url VARCHAR(255) DEFAULT NULL');
        $this->addSql('DROP INDEX uniq_260ca7fe7927c74');
        $this->addSql('DROP INDEX uniq_260ca7ff85e0677');
        $this->addSql('ALTER TABLE pia_user ADD username_canonical VARCHAR(180) NULL'); // TEMP AS NULLABLE FIELD
        $this->addSql('ALTER TABLE pia_user ADD email_canonical VARCHAR(180) NULL'); // TEMP AS NULLABLE FIELD
        $this->addSql('ALTER TABLE pia_user ADD salt VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE pia_user ADD last_login TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE pia_user ADD confirmation_token VARCHAR(180) DEFAULT NULL');
        $this->addSql('ALTER TABLE pia_user ADD password_requested_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE pia_user ALTER username TYPE VARCHAR(180)');
        $this->addSql('ALTER TABLE pia_user ALTER email TYPE VARCHAR(180)');

        $this->addSql('UPDATE pia_user SET username_canonical = username');
        $this->addSql('UPDATE pia_user SET email_canonical = email');

        $this->addSql('ALTER TABLE pia_user ALTER COLUMN username_canonical SET NOT NULL');
        $this->addSql('ALTER TABLE pia_user ALTER COLUMN email_canonical SET NOT NULL');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_260CA7F92FC23A8 ON pia_user (username_canonical)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_260CA7FA0D96FBF ON pia_user (email_canonical)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_260CA7FC05FB297 ON pia_user (confirmation_token)');
    }

    protected function Version20180515154624_down(): void
    {
        $this->addSql('ALTER TABLE oauth_client DROP url');
        $this->addSql('DROP INDEX UNIQ_260CA7F92FC23A8');
        $this->addSql('DROP INDEX UNIQ_260CA7FA0D96FBF');
        $this->addSql('DROP INDEX UNIQ_260CA7FC05FB297');
        $this->addSql('ALTER TABLE pia_user DROP username_canonical');
        $this->addSql('ALTER TABLE pia_user DROP email_canonical');
        $this->addSql('ALTER TABLE pia_user DROP salt');
        $this->addSql('ALTER TABLE pia_user DROP last_login');
        $this->addSql('ALTER TABLE pia_user DROP confirmation_token');
        $this->addSql('ALTER TABLE pia_user DROP password_requested_at');
        $this->addSql('ALTER TABLE pia_user ALTER username TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE pia_user ALTER email TYPE VARCHAR(255)');
        $this->addSql('CREATE UNIQUE INDEX uniq_260ca7fe7927c74 ON pia_user (email)');
        $this->addSql('CREATE UNIQUE INDEX uniq_260ca7ff85e0677 ON pia_user (username)');
    }

    protected function Version20180516134220_up(): void
    {
        $this->addSql('CREATE SEQUENCE pia_structure_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE pia_structure (id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE pia_user ADD structure_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE pia_user ADD CONSTRAINT FK_260CA7F2534008B FOREIGN KEY (structure_id) REFERENCES pia_structure (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_260CA7F2534008B ON pia_user (structure_id)');
        $this->addSql('ALTER TABLE pia ADD structure_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE pia ADD CONSTRAINT FK_253A30622534008B FOREIGN KEY (structure_id) REFERENCES pia_structure (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_253A30622534008B ON pia (structure_id)');
    }

    protected function Version20180516134220_down(): void
    {
        $this->addSql('ALTER TABLE pia_user DROP CONSTRAINT FK_260CA7F2534008B');
        $this->addSql('ALTER TABLE pia DROP CONSTRAINT FK_253A30622534008B');
        $this->addSql('DROP SEQUENCE pia_structure_id_seq CASCADE');
        $this->addSql('DROP TABLE pia_structure');
        $this->addSql('DROP INDEX IDX_260CA7F2534008B');
        $this->addSql('ALTER TABLE pia_user DROP structure_id');
        $this->addSql('DROP INDEX IDX_253A30622534008B');
        $this->addSql('ALTER TABLE pia DROP structure_id');
    }

    protected function Version20180517132216_up(): void
    {
        $this->addSql('CREATE SEQUENCE pia_structure_type_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE pia_structure_type (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE pia_structure ADD type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE pia_structure ADD CONSTRAINT FK_5036DBE6C54C8C93 FOREIGN KEY (type_id) REFERENCES pia_structure_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_5036DBE6C54C8C93 ON pia_structure (type_id)');
    }

    protected function Version20180517132216_down(): void
    {
        $this->addSql('ALTER TABLE pia_structure DROP CONSTRAINT FK_5036DBE6C54C8C93');
        $this->addSql('DROP SEQUENCE pia_structure_type_id_seq CASCADE');
        $this->addSql('DROP TABLE pia_structure_type');
        $this->addSql('DROP INDEX IDX_5036DBE6C54C8C93');
        $this->addSql('ALTER TABLE pia_structure DROP type_id');
    }

    protected function Version20180522073548_up(): void
    {
        $this->addSql('CREATE SEQUENCE pia_profile_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE pia_profile (id INT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, pia_roles TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6372CD59A76ED395 ON pia_profile (user_id)');
        $this->addSql('COMMENT ON COLUMN pia_profile.pia_roles IS \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE pia_profile ADD CONSTRAINT FK_6372CD59A76ED395 FOREIGN KEY (user_id) REFERENCES pia_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    protected function Version20180522073548_down(): void
    {
        $this->addSql('DROP SEQUENCE pia_profile_id_seq CASCADE');
        $this->addSql('DROP TABLE pia_profile');
    }

    protected function Version20180524073608_up(): void
    {
        $this->addSql('ALTER TABLE pia_profile ALTER name DROP NOT NULL');
    }

    protected function Version20180524073608_down(): void
    {
        $this->addSql('ALTER TABLE pia_profile ALTER name SET NOT NULL');
    }

    protected function Version20180524074718_up(): void
    {
        $this->addSql('ALTER TABLE pia_profile ADD last_name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE pia_profile DROP pia_roles');
        $this->addSql('ALTER TABLE pia_profile RENAME COLUMN name TO first_name');
    }

    protected function Version20180524074718_down(): void
    {
        $this->addSql('ALTER TABLE pia_profile ADD name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE pia_profile ADD pia_roles TEXT NOT NULL');
        $this->addSql('ALTER TABLE pia_profile DROP first_name');
        $this->addSql('ALTER TABLE pia_profile DROP last_name');
        $this->addSql('COMMENT ON COLUMN pia_profile.pia_roles IS \'(DC2Type:json)\'');
    }

    protected function Version20180524080353_up(): void
    {
        $this->addSql('ALTER TABLE pia_profile ADD pia_roles TEXT NOT NULL');
        $this->addSql('COMMENT ON COLUMN pia_profile.pia_roles IS \'(DC2Type:json)\'');
    }

    protected function Version20180524080353_down(): void
    {
        $this->addSql('ALTER TABLE pia_profile DROP pia_roles');
    }

    protected function Version20180524100033_up(): void
    {
        $this->addSql('ALTER TABLE pia_profile DROP pia_roles');
    }

    protected function Version20180524100033_down(): void
    {
        $this->addSql('ALTER TABLE pia_profile ADD pia_roles TEXT NOT NULL');
        $this->addSql('COMMENT ON COLUMN pia_profile.pia_roles IS \'(DC2Type:json)\'');
    }

    protected function Version20180524152449_up(): void
    {
        $this->addSql('CREATE SEQUENCE pia_template_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE pia_template (id INT NOT NULL, enabled BOOLEAN NOT NULL, name VARCHAR(255) NOT NULL, description TEXT NULL, data TEXT NOT NULL, imported_file_name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN pia_template.data IS \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE pia ADD template_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE pia ADD CONSTRAINT FK_253A30625DA0FB8 FOREIGN KEY (template_id) REFERENCES pia_template (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_253A30625DA0FB8 ON pia (template_id)');
    }

    protected function Version20180524152449_down(): void
    {
        $this->addSql('ALTER TABLE pia DROP CONSTRAINT FK_253A30625DA0FB8');
        $this->addSql('DROP SEQUENCE pia_template_id_seq CASCADE');
        $this->addSql('DROP TABLE pia_template');
        $this->addSql('DROP INDEX IDX_253A30625DA0FB8');
        $this->addSql('ALTER TABLE pia DROP template_id');
    }

    protected function Version20180528094424_up(): void
    {
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

    protected function Version20180528094424_down(): void
    {
        $this->addSql('DROP TABLE pia_templates__structures');
        $this->addSql('DROP TABLE pia_templates__structure_types');
    }

    protected function Version20180528125823_up(): void
    {
        $this->addSql('ALTER TABLE pia_profile ALTER last_name DROP NOT NULL');
    }

    protected function Version20180528125823_down(): void
    {
        $this->addSql('ALTER TABLE pia_profile ALTER last_name SET NOT NULL');
    }

    protected function Version20180528142132_up(): void
    {
        $this->addSql('ALTER TABLE pia_profile RENAME TO user_profile');
        $this->addSql('CREATE SEQUENCE user_profile_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER INDEX uniq_6372cd59a76ed395 RENAME TO UNIQ_D95AB405A76ED395');
    }

    protected function Version20180528142132_down(): void
    {
        $this->addSql('ALTER TABLE user_profile RENAME TO pia_profile');
        $this->addSql('ALTER INDEX UNIQ_D95AB405A76ED395 RENAME TO uniq_6372cd59a76ed395');
        $this->addSql('DROP SEQUENCE user_profile_id_seq');
    }

    protected function Version20180530091757_up(): void
    {
        $this->addSql('CREATE SEQUENCE pia_folder_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE pia_folder (id INT NOT NULL, name VARCHAR(255) NOT NULL, tree_root INT DEFAULT NULL, parent_id INT DEFAULT NULL, lft INT NOT NULL, lvl INT NOT NULL, rgt INT NOT NULL, structure_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_71BF4B04A977936C ON pia_folder (tree_root)');
        $this->addSql('CREATE INDEX IDX_71BF4B04727ACA70 ON pia_folder (parent_id)');
        $this->addSql('ALTER TABLE pia_folder ADD CONSTRAINT FK_71BF4B04A977936C FOREIGN KEY (tree_root) REFERENCES pia_folder (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pia_folder ADD CONSTRAINT FK_71BF4B04727ACA70 FOREIGN KEY (parent_id) REFERENCES pia_folder (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pia_folder ADD CONSTRAINT FK_71BF4B042534008B FOREIGN KEY (structure_id) REFERENCES pia_structure (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pia ADD folder_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE pia ADD CONSTRAINT FK_253A3062162CB942 FOREIGN KEY (folder_id) REFERENCES pia_folder (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_253A3062162CB942 ON pia (folder_id)');
        $this->addSql('CREATE INDEX IDX_71BF4B042534008B ON pia_folder (structure_id)');
    }

    protected function Version20180530091757_down(): void
    {
        $this->addSql('ALTER TABLE pia DROP CONSTRAINT FK_253A3062162CB942');
        $this->addSql('ALTER TABLE pia_folder DROP CONSTRAINT FK_71BF4B042534008B');
        $this->addSql('ALTER TABLE pia_folder DROP CONSTRAINT FK_71BF4B04A977936C');
        $this->addSql('ALTER TABLE pia_folder DROP CONSTRAINT FK_71BF4B04727ACA70');
        $this->addSql('DROP SEQUENCE pia_folder_id_seq CASCADE');
        $this->addSql('DROP INDEX IDX_71BF4B042534008B');
        $this->addSql('DROP TABLE pia_folder');
        $this->addSql('DROP INDEX IDX_253A3062162CB942');
        $this->addSql('ALTER TABLE pia DROP folder_id');
    }

    protected function Version20180530095437_up(): void
    {
        // Create and associate mandatory rootFolders for each structures

        /** @var RegistryInterface $doctrine */
        $doctrine = $this->container->get('doctrine');
        $structures = $doctrine->getRepository(Structure::class)->findAll();

        /** @var Structure $structure */
        foreach ($structures as $structure) {
            if ($structure->getRootFolder() === null) {
                $rootFolder = new Folder('root', $structure);
                $doctrine->getManager()->persist($rootFolder);
                $doctrine->getManager()->flush($rootFolder);
            }
        }
    }

    protected function Version20180530095437_down(): void
    {
        // Dissociate mandatory rootFolders for each structures

        /** @var RegistryInterface $doctrine */
        $doctrine = $this->container->get('doctrine');
        $structures = $doctrine->getRepository(Structure::class)->findAll();

        /** @var Structure $structure */
        foreach ($structures as $structure) {
            if (($rootFolder = $structure->getRootFolder()) !== null) {
                $rootFolder->setStructure(null);
                $doctrine->getManager()->flush($rootFolder);
            }
        }
    }

    protected function Version20180605082149_up(): void
    {
        /** @var RegistryInterface $doctrine */
        $doctrine = $this->container->get('doctrine');
        $structures = $doctrine->getRepository(Structure::class)->findAll();

        /** @var Structure $structure */
        foreach ($structures as $structure) {
            $rootFolder = $structure->getRootFolder();
            /** @var Pia $pia */
            foreach ($structure->getPias() as $pia) {
                $pia->setFolder($rootFolder);
            }
        }

        $doctrine->getManager()->flush();
    }

    protected function Version20180605082149_down(): void
    {
        /** @var RegistryInterface $doctrine */
        $doctrine = $this->container->get('doctrine');
        $pias = $doctrine->getRepository(Pia::class)->findAll();

        /** @var Pia $pia */
        foreach ($pias as $pia) {
            $pia->setFolder(null);
        }

        $doctrine->getManager()->flush();
    }

    public function setContainer(?ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
