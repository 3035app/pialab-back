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
use Symfony\Bridge\Doctrine\RegistryInterface;
use PiaApi\Entity\Oauth\User;
use PiaApi\Entity\Pia\UserProfile;

class Version1_1_0 extends AbstractMigration implements ContainerAwareInterface
{
    use
        ContainerAwareTrait,
        MigrationTrait
    ;

    private $migrations = [
        'schema' => [
        ],
        'data' => [
            '20180619143737',
        ],
    ];

    // #########################################
    //         OLD VERSIONS BELOW
    // #########################################

    protected function Version20180619143737_up(Schema $schema): void
    {
        /** @var RegistryInterface $doctrine */
        $doctrine = $this->container->get('doctrine');
        $users = $doctrine->getRepository(User::class)->findAll();

        /** @var User $user */
        foreach ($users as $user) {
            if ($user->getProfile() === null) {
                $profile = new UserProfile();
                $profile->setUser($user);
                $user->setProfile($profile);

                $doctrine->getManager()->persist($profile);
                $doctrine->getManager()->flush();
            }
        }

        $emptyProfile = $doctrine->getRepository(UserProfile::class)->findBy(['user' => null]);

        /** @var UserProfile $profile */
        foreach ($emptyProfile as $profile) {
            $doctrine->getManager()->remove($profile);
        }
        $doctrine->getManager()->flush();
    }

    protected function Version20180619143737_down(Schema $schema): void
    {
        // Do nothing
    }
}
