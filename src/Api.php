<?php

namespace mattvb91\TronTrx;

use GuzzleHttp\Client;
use mattvb91\TronTrx\Exceptions\TronErrorException;

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

    public function post(string $endpoint, array $data = [], bool $returnAssoc = false)
    {
        if (sizeof($data)) {
            $data = ['json' => $data];
        }

        $stream = (string)$this->getClient()->post($endpoint, $data)->getBody();
        $body = json_decode($stream, $returnAssoc);

        if ($returnAssoc && isset($body['Error'])) {
            throw new TronErrorException($body['Error']);
        } elseif (isset($body->Error)) {
            throw new TronErrorException($body->Error);
        }

        return $body;
    }
}