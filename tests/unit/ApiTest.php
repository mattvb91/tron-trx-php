<?php

use GuzzleHttp\Client;
use mattvb91\TronTrx\Api;
use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
    /**
     * @covers \mattvb91\TronTrx\Api::getClient
     * @covers \mattvb91\TronTrx\Api::__construct
     */
    public function testGetClientReturnsInstanceOfClient()
    {
        $api = new Api(new Client());
        $this->assertInstanceOf(Client::class, $api->getClient());
    }
}