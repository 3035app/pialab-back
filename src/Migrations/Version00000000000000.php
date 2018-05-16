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

class Version00000000000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        if ($schema->hasTable('pia_user')) {
            return;
        }

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
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        if (!$schema->hasTable('pia_user')) {
            return;
        }

        $this->addSql('DROP SCHEMA public CASCADE;');
        $this->addSql('CREATE SCHEMA public;');
    }
}
