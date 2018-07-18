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

class Version1_1_0 extends AbstractMigration implements ContainerAwareInterface
{
    use
        ContainerAwareTrait,
        MigrationTrait
    ;

    private $migrations = [
        'schema' => [
            '20180612141711',
        ],
        'data' => [
            '20180619143737',
        ],
    ];

    // #########################################
    //         OLD VERSIONS BELOW
    // #########################################

    protected function Version20180612141711_up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE pia ADD type VARCHAR(255) NULL');
        $this->addSql("UPDATE pia set type='advanced'");
        $this->addSql('ALTER TABLE pia ALTER COLUMN type SET NOT NULL');
    }

    protected function Version20180612141711_down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE pia DROP type');
    }

    protected function Version20180619143737_up(Schema $schema): void
    {
        // Fetch users without profile
        $users = $this->connection->executeQuery('
            SELECT
                u.id
            FROM 
                pia_user u
            LEFT JOIN 
                user_profile p ON 
                    p.user_id = u.id
            WHERE
                p.id IS NULL
        ')->fetchAll();

        foreach ($users as $user) {
            $this->connection->executeQuery('INSERT INTO user_profile(id, user_id, created_at, updated_at) VALUES (nextval(\'user_profile_id_seq\'), ' . $user['id'] . ', NOW(), NOW())');
        }

        // Remove profile without users
        $this->connection->executeQuery('DELETE FROM user_profile WHERE user_id IS NULL');
    }

    protected function Version20180619143737_down(Schema $schema): void
    {
        // Do nothing
    }
}
