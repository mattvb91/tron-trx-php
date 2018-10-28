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
            ])->getBody();

        $body = json_decode($body);

        return $body->result;
    }

    public function getAccount(Address $address): Account
    {
        $body = (string)$this->_api->getClient()
            ->post('/wallet/getaccount', [
                'json' => [
                    'address' => $address->hexAddress,
                ],
            ])
            ->getBody();

        $body = json_decode($body);

        $address = new Address($body->address);

        return new Account($address, $body->balance, $body->create_time);
    }

    public function getAccountNet(Address $address): ?array
    {
        $body = (string)$this->_api->getClient()
            ->post('/wallet/getaccountnet', [
                'json' => [
                    'address' => $address->hexAddress,
                ],
            ])
            ->getBody();

        $data = json_decode($body, true);

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
        $body = (string)$this->_api->getClient()
            ->post('/wallet/createtransaction', [
                'json' => [
                    'to_address'    => $toAddress->hexAddress,
                    'owner_address' => $ownerAddress->hexAddress,
                    'amount'        => $amount,
                ],
            ])->getBody();

        $body = json_decode($body);

        //TODO move to api request
//        if (isset($body->Error)) {
//            throw new TronErrorException($body->Error);
//        }

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

        $body = (string)$this->_api->getClient()
            ->post('/wallet/gettransactionsign', [
                'json' => [
                    'transaction' => $transactionArray,
                    'privateKey'  => $privateKey,
                ],
            ])->getBody();

        $body = json_decode($body);

        $transaction->signature = $body->signature;

        return $transaction;
    }

    public function broadcastTransaction(Transaction $transaction): bool
    {
        if (!$transaction->isSigned()) {
            throw new TransactionException('Transaction is not signed');
        }

        $transactionArray = json_decode(json_encode($transaction), true);

        $body = (string)$this->_api->getClient()
            ->post('/wallet/broadcasttransaction', [
                'json' => $transactionArray,
            ])->getBody();

        $body = json_decode($body);

        return $body->result ? $body->result : false;
    }
}