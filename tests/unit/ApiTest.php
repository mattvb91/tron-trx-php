<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use mattvb91\TronTrx\Api;
use mattvb91\TronTrx\Exceptions\TronErrorException;
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

    /**
     * @covers \mattvb91\TronTrx\Api::post
     */
    public function testPostAssocTrueFalse()
    {
        // Create a mock and queue two responses.
        $response = new Response(200, [], json_encode([
            'test' => true,
        ]));

        $mock = new MockHandler([
            $response,
            $response,
            new Response(200, [], json_encode(['Error' => 'Error'])),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $api = new Api($client);
        $this->assertArrayHasKey('test', $api->post('/test', ['data' => []], true));

        $response = $api->post('/test');
        $this->assertObjectHasAttribute('test', $response);
        $this->assertTrue($response->test);
    }

    /**
     * @covers \mattvb91\TronTrx\Api::post
     */
    public function testErrorExceptionIsThrownWithAssoc()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode(['Error' => 'Error'])),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $api = new Api($client);

        $this->expectException(TronErrorException::class);
        $api->post('/test', [], true);
    }

    /**
     * @covers \mattvb91\TronTrx\Api::post
     */
    public function testErrorExceptionIsThrownWithObject()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode(['Error' => 'Error'])),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $api = new Api($client);

        $this->expectException(TronErrorException::class);
        $api->post('/test');
    }
}