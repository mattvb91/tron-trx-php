<?php

namespace mattvb91\TronTrx;

class Transaction
{
    public $signature = [];
    public $txID = '';
    public $rawData = \stdClass::class;

    public function __construct(string $txID, \stdClass $rawData)
    {
        $this->txID = $txID;
        $this->rawData = $rawData;
    }
}