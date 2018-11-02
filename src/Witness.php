<?php

namespace mattvb91\TronTrx;

/**
 * Class Witness
 * @package mattvb91\TronTrx
 */
class Witness
{
    public $address, $url = '';
    public $voteCount, $totalProduced, $latestBlockNum, $latestSlotNum;

    public function __construct(
        string $address,
        string $url,
        int $voteCount = null,
        int $totalProduced = null,
        int $latestBlockNum = null,
        int $latestSlotNum = null)
    {
        $this->address = $address;
        $this->voteCount = $voteCount;
        $this->url = $url;
        $this->totalProduced = $totalProduced;
        $this->latestBlockNum = $latestBlockNum;
        $this->latestSlotNum = $latestSlotNum;
    }
}