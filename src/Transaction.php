<?php

namespace mattvb91\TronTrx;

class Transaction
{
    public $signature = [];
    public $txID = '';
    public $raw_data = \stdClass::class;

    public function __construct(string $txID, \stdClass $rawData)
    {
        $this->txID = $txID;
        $this->raw_data = $rawData;
    }

    public function isSigned(): bool
    {
        return (bool)sizeof($this->signature);
    }
}