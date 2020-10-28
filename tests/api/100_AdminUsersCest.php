<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use Codeception\Util\HttpCode;

/**
 * @group all
 * @group api
 * @group api_admin_users
 */
class AdminUsersCest
{
    /**
     * @var array
     */
    private $userData = [
        'email'    => 'api-ci@pia.io',
        'password' => 'api-ci',
    ];

    /**
     * @var array
     */
    private $userJsonType = [
        'id'                    => 'integer',
        'username'              => 'string',
        'username_canonical'    => 'string',
        'email'                 => 'string',
        'email_canonical'       => 'string',
        'enabled'               => 'boolean',
        'salt'                  => 'string|null',
        'password'              => 'string',
        'plain_password'        => 'string|null',
        'last_login'            => 'string|null',
        'confirmation_token'    => 'string|null',
        'password_requested_at' => 'string|null',
        'groups'                => 'array|null',
        'roles'                 => 'array',
        'creation_date'         => 'string',
        'expiration_date'       => 'string',
        'locked'                => 'boolean',
        'profile'               => 'array',
        'application'           => 'array|null',
        'structure'             => 'array|null',
    ];

    private $user = [];

    public function create_user_test(ApiTester $I)
    {
        $I->amGoingTo('Create a new User');

        $I->login();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/users', $this->userData);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType($this->userJsonType);
        $I->seeResponseContainsJson([
            'email' => $this->userData['email'],
        ]);

        $this->user = json_decode(json_encode($I->getPreviousResponse()), JSON_OBJECT_AS_ARRAY);
    }

    /**
     * @depends create_user_test
     */
    public function show_user_test(ApiTester $I)
    {
        $I->amGoingTo('Show newly created User, with id: ' . $this->user['id']);

        $I->login();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/users/' . $this->user['id']);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType($this->userJsonType);
        $I->seeResponseContainsJson([
            'email' => $this->userData['email'],
            'id'    => $this->user['id'],
        ]);
    }

    /**
     * @depends create_user_test
     */
    public function list_users_test(ApiTester $I)
    {
        $I->amGoingTo('Show all Users');

        $I->login();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/users');

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }

    /**
     * @depends create_user_test
     */
    public function edit_user_test(ApiTester $I)
    {
        $I->amGoingTo('Edit newly created User, with id: ' . $this->user['id']);

        $I->login();

        $username = $this->user['username'] . '-edited';

        $data = array_merge($this->user, [
            'username' => $username,
        ]);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPUT('/users/' . $this->user['id'], $data);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType($this->userJsonType);
        $I->seeResponseContainsJson([
            'username' => $username,
        ]);
    }

    /*
     * @depends create_user_test
     */
    public function remove_user_test(ApiTester $I)
    {
        $I->amGoingTo('Remove User, with id: ' . $this->user['id']);

        $I->login();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendDELETE('/users/' . $this->user['id']);

        $I->seeResponseCodeIs(HttpCode::OK);
    }
}
