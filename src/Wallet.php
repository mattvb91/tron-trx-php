<?php

namespace mattvb91\TronTrx;

use mattvb91\TronTrx\Exceptions\TransactionException;
use mattvb91\TronTrx\Interfaces\WalletInterface;

/**
 * Class Wallet
 * @package mattvb91\TronTrx
 */
class Wallet implements WalletInterface
{
    private $_api;

    public function __construct(Api $_api)
    {
        $this->_api = $_api;
    }

    public function generateAddress(): Address
    {
        $body = $this->_api->post('/wallet/generateaddress');

        return new Address($body->address, $body->privateKey, $body->hexAddress);
    }

    public function validateAddress(Address $address): bool
    {
        $body = $this->_api->post('/wallet/validateaddress', [
            'address' => $address->address,
        ]);

        return $body->result;
    }

    public function getAccount(Address $address): Account
    {
        $body = $this->_api->post('/wallet/getaccount', [
            'address' => $address->hexAddress,
        ]);

        $address = new Address($body->address);

        return new Account($address, $body->balance, $body->create_time);
    }

    public function getAccountNet(Address $address): ?array
    {
        $data = $this->_api->post('/wallet/getaccountnet',
            ['address' => $address->hexAddress],
            true
        );

        if (sizeof($data)) {
            return $data;
        }

        return null;
    }

    /**
     * This is open to attacks. Instead use /wallet/createtransaction,
     * then sign it locally,
     * then /wallet/broadcasttransaction
     *
     * @deprecated See exception
     */
    final public function easyTransferByPrivate(string $private, Address $address, float $amount)
    {
        throw new \Exception('This is vulnerable to MiTM attacks. Do not use');
    }

    public function createTransaction(Address $toAddress, Address $ownerAddress, float $amount = 0): Transaction
    {
        $body = $this->_api->post('/wallet/createtransaction', [
            'to_address'    => $toAddress->hexAddress,
            'owner_address' => $ownerAddress->hexAddress,
            'amount'        => $amount,
        ]);

        return new Transaction(
            $body->txID,
            $body->raw_data
        );
    }

    /**
     * TODO sign locally instead of over api as this is man in the middle attack
     * waiting to happen by posting privateKey
     */
    public function signTransaction(Transaction &$transaction, string $privateKey): Transaction
    {
        unset($transaction->signature);
        $transactionArray = json_decode(json_encode($transaction), true);

        $body = $this->_api->post('/wallet/gettransactionsign', [
            'transaction' => $transactionArray,
            'privateKey'  => $privateKey,
        ]);

        $transaction->signature = $body->signature;

        return $transaction;
    }

    public function broadcastTransaction(Transaction $transaction): bool
    {
        if (!$transaction->isSigned()) {
            throw new TransactionException('Transaction is not signed');
        }

        $transactionArray = json_decode(json_encode($transaction), true);
        $body = $this->_api->post('/wallet/broadcasttransaction', $transactionArray);

        return $body->result ? $body->result : false;
    }
}