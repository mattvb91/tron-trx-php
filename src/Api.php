<?php

namespace mattvb91\TronTrx;

use GuzzleHttp\Client;

/**
 * Class Api
 * @package mattvb91\TronTrx
 */
class Api
{
    private $_client;

    public function __construct(Client $client)
    {
        $this->_client = $client;
    }

    public function getClient(): Client
    {
        return $this->_client;
    }
}