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

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180515154624 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        if ($schema->getTable('pia_user')->hasColumn('username_canonical')) {
            return;
        }

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

    public function down(Schema $schema)
    {
        if (!$schema->getTable('pia_user')->hasColumn('username_canonical')) {
            return;
        }

        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

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
}
