<?php

namespace mattvb91\TronTrx;

/**
 * Class Address
 * @package mattvb91\TronTrx
 */
class Address
{
    public $privateKey,
        $address,
        $hexAddress = '';

    public function __construct(string $address = '', string $privateKey = '', string $hexAddress = '')
    {
        if (strlen($address) === 0) {
            throw new \InvalidArgumentException('Address can not be empty');
        }

        $this->privateKey = $privateKey;
        $this->address = $address;
        $this->hexAddress = $hexAddress;
    }
}