<?php

use GuzzleHttp\Client;
use mattvb91\TronTrx\Address;
use mattvb91\TronTrx\Api;
use PHPUnit\Framework\TestCase;

class WalletTest extends TestCase
{
    private $_api;

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->_api = new Api(new Client([
            'base_uri' => 'http://192.168.178.26:8090',
        ]));
    }

    /**
     * @covers \mattvb91\TronTrx\Wallet::__construct
     * @covers \mattvb91\TronTrx\Address::__construct
     * @covers \mattvb91\TronTrx\Wallet::generateAddress
     */
    public function testGenerateAddress()
    {
        $wallet = new mattvb91\TronTrx\Wallet($this->_api);

        $address = $wallet->generateAddress();
        $this->assertInstanceOf(Address::class, $address);

        return $address;
    }

    /**
     * @depends testGenerateAddress
     * @covers  \mattvb91\TronTrx\Wallet::validateAddress
     */
    public function testAddressValidation(Address $address)
    {
        $wallet = new mattvb91\TronTrx\Wallet($this->_api);

        $this->assertTrue($wallet->validateAddress($address));
    }
}
