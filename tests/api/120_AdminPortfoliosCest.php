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
 * @group api_admin_portfolios
 */
class AdminPortfoliosCest
{
    /**
     * @var array
     */
    private $portfolioData = [
        'name' => 'PortfolioCI',
    ];

    /**
     * @var array
     */
    private $portfolioJsonType = [
        'id'         => 'integer',
        'name'       => 'string',
        'created_at' => 'string',
        'updated_at' => 'string',
    ];

    private $portfolio = [];

    public function create_portfolio_test(ApiTester $I)
    {
        $I->amGoingTo('Create a new Portfolio');

        $I->login();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/portfolios', $this->portfolioData);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType($this->portfolioJsonType);
        $I->seeResponseContainsJson([
            'name' => $this->portfolioData['name'],
        ]);

        $this->portfolio = json_decode(json_encode($I->getPreviousResponse()), JSON_OBJECT_AS_ARRAY);
    }

    /**
     * @depends create_portfolio_test
     */
    public function show_portfolio_test(ApiTester $I)
    {
        $I->amGoingTo('Show newly created Portfolio, with id: ' . $this->portfolio['id']);

        $I->login();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/portfolios/' . $this->portfolio['id']);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType($this->portfolioJsonType);
        $I->seeResponseContainsJson([
            'name'  => $this->portfolioData['name'],
            'id'    => $this->portfolio['id'],
        ]);
    }

    /**
     * @depends create_portfolio_test
     */
    public function list_portfolios_test(ApiTester $I)
    {
        $I->amGoingTo('Show all Portfolios');

        $I->login();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('/portfolios');

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }

    /**
     * @depends create_portfolio_test
     */
    public function edit_portfolio_test(ApiTester $I)
    {
        $I->amGoingTo('Edit newly created Portfolio, with id: ' . $this->portfolio['id']);

        $I->login();

        $name = $this->portfolio['name'] . '-edited';

        $data = array_merge($this->portfolio, [
            'name' => $name,
        ]);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPUT('/portfolios/' . $this->portfolio['id'], $data);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType($this->portfolioJsonType);
        $I->seeResponseContainsJson([
            'name' => $name,
        ]);
    }

    /*
     * @depends create_portfolio_test
     */
    public function remove_portfolio_test(ApiTester $I)
    {
        $I->amGoingTo('Remove Portfolio, with id: ' . $this->portfolio['id']);

        $I->login();

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendDELETE('/portfolios/' . $this->portfolio['id']);

        $I->seeResponseCodeIs(HttpCode::OK);
    }
}
