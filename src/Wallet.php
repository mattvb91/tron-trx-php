<?php

namespace mattvb91\TronTrx;

/**
 * Class Wallet
 * @package mattvb91\TronTrx
 */
class Wallet
{
    private $_api;

    public function __construct(Api $_api)
    {
        $this->_api = $_api;
    }

    public function generateAddress(): Address
    {
        $body = $this->_api->getClient()
            ->request('POST', '/wallet/generateaddress')
            ->getBody();

        $body = json_decode($body);

        return new Address($body->address, $body->privateKey, $body->hexAddress);
    }

    public function validateAddress(Address $address): bool
    {
        $body = $this->_api->getClient()
            ->post('/wallet/validateaddress', [
                'json' => [
                    'address' => $address->address,
                ],
            ])
            ->getBody();

        $body = json_decode($body);

        return $body->result;
    }
}