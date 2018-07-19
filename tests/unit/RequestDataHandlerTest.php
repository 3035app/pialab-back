<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use PiaApi\DataHandler\RequestDataHandler;

class RequestDataHandlerTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected $datas = [
        RequestDataHandler::TYPE_STRING => 'testString',
        RequestDataHandler::TYPE_BOOL   => true,
        RequestDataHandler::TYPE_INT    => 42,
        RequestDataHandler::TYPE_ARRAY  => ['testArray'],
        \DateTime::class                => '2018-07-19T12:38:19+02:00',
        \DateTimeImmutable::class       => '2018-07-20T12:38:19+02:00',
    ];

    public function test_string_data()
    {
        $requestDataHandler = new RequestDataHandler($this->datas[RequestDataHandler::TYPE_STRING], RequestDataHandler::TYPE_STRING);

        $this->assertEquals($this->datas[RequestDataHandler::TYPE_STRING], $requestDataHandler->getValue());
    }

    public function test_nullable_string_data()
    {
        $requestDataHandler = new RequestDataHandler(null, RequestDataHandler::TYPE_NULLABLE_STRING);

        $this->assertNull($requestDataHandler->getValue());
    }

    public function test_default_string_data()
    {
        $requestDataHandler = new RequestDataHandler(null, RequestDataHandler::TYPE_STRING);

        $this->assertEquals('', $requestDataHandler->getValue());
    }

    public function test_bool_data()
    {
        $requestDataHandler = new RequestDataHandler($this->datas[RequestDataHandler::TYPE_BOOL], RequestDataHandler::TYPE_BOOL);

        $this->assertTrue($requestDataHandler->getValue());
    }

    public function test_nullable_bool_data()
    {
        $requestDataHandler = new RequestDataHandler(null, RequestDataHandler::TYPE_NULLABLE_BOOL);

        $this->assertNull($requestDataHandler->getValue());
    }

    public function test_default_bool_data()
    {
        $requestDataHandler = new RequestDataHandler(null, RequestDataHandler::TYPE_BOOL);

        $this->assertEquals(false, $requestDataHandler->getValue());
    }

    public function test_int_data()
    {
        $requestDataHandler = new RequestDataHandler($this->datas[RequestDataHandler::TYPE_INT], RequestDataHandler::TYPE_INT);

        $this->assertEquals($this->datas[RequestDataHandler::TYPE_INT], $requestDataHandler->getValue());
    }

    public function test_nullable_int_data()
    {
        $requestDataHandler = new RequestDataHandler(null, RequestDataHandler::TYPE_NULLABLE_INT);

        $this->assertNull($requestDataHandler->getValue());
    }

    public function test_default_int_data()
    {
        $requestDataHandler = new RequestDataHandler(null, RequestDataHandler::TYPE_INT);

        $this->assertEquals(0, $requestDataHandler->getValue());
    }

    public function test_array_data()
    {
        $requestDataHandler = new RequestDataHandler($this->datas[RequestDataHandler::TYPE_ARRAY], RequestDataHandler::TYPE_ARRAY);

        $this->assertEquals($this->datas[RequestDataHandler::TYPE_ARRAY], $requestDataHandler->getValue());
    }

    public function test_nullable_array_data()
    {
        $requestDataHandler = new RequestDataHandler(null, RequestDataHandler::TYPE_NULLABLE_ARRAY);

        $this->assertNull($requestDataHandler->getValue());
    }

    public function test_default_array_data()
    {
        $requestDataHandler = new RequestDataHandler(null, RequestDataHandler::TYPE_ARRAY);

        $this->assertEquals([], $requestDataHandler->getValue());
    }

    public function test_datetime_data()
    {
        $requestDataHandler = new RequestDataHandler($this->datas[\DateTime::class], \DateTime::class);

        $this->assertEquals(new DateTime($this->datas[\DateTime::class]), $requestDataHandler->getValue());
    }

    public function test_nullable_datetime_data()
    {
        $requestDataHandler = new RequestDataHandler(null, \DateTime::class);

        $this->assertNull($requestDataHandler->getValue());
    }

    public function test_datetime_immutable_data()
    {
        $requestDataHandler = new RequestDataHandler($this->datas[\DateTimeImmutable::class], \DateTimeImmutable::class);

        $this->assertEquals(new DateTime($this->datas[\DateTimeImmutable::class]), $requestDataHandler->getValue());
    }

    public function test_nullable_datetime_immutable_data()
    {
        $requestDataHandler = new RequestDataHandler(null, \DateTimeImmutable::class);

        $this->assertNull($requestDataHandler->getValue());
    }
}
