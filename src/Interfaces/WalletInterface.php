<?php

namespace mattvb91\TronTrx\Interfaces;

use mattvb91\TronTrx\Account;
use mattvb91\TronTrx\Address;
use mattvb91\TronTrx\Transaction;

/**
 * Interface WalletInterface
 * @package mattvb91\TronTrx\Interfaces
 */
interface WalletInterface
{
    public function generateAddress(): Address;

    public function validateAddress(Address $address): bool;

    public function getAccount(Address $address): Account;

    public function easyTransferByPrivate(string $private, Address $address, float $amount);

    public function createTransaction(Address $toAddress, Address $ownerAddress, float $amount = 0): Transaction;

    public function signTransaction(Transaction &$transaction, string $privateKey): Transaction;
}