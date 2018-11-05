<?php

namespace mattvb91\TronTrx;

use kornrunner\Keccak;
use mattvb91\TronTrx\Exceptions\TransactionException;
use mattvb91\TronTrx\Exceptions\TronErrorException;
use mattvb91\TronTrx\Interfaces\WalletInterface;
use mattvb91\TronTrx\Support\Base58;
use mattvb91\TronTrx\Support\Base58Check;
use mattvb91\TronTrx\Support\Crypto;
use mattvb91\TronTrx\Support\Hash;
use mattvb91\TronTrx\Traits\TronAwareTrait;
use Phactor\Key;

/**
 * Class Wallet
 * @package mattvb91\TronTrx
 */
class Wallet implements WalletInterface
{
    use TronAwareTrait;

    private $_api;

    public function __construct(Api $_api)
    {
        $this->_api = $_api;
    }

    public function genKeyPair(): array
    {
        $key = new Key();

        return $key->GenerateKeypair();
    }

    public function getAddressHex(string $pubKeyBin): string
    {
        if (strlen($pubKeyBin) == 65) {
            $pubKeyBin = substr($pubKeyBin, 1);
        }

        $hash = Keccak::hash($pubKeyBin, 256);

        return Address::ADDRESS_PREFIX . substr($hash, 24);
    }

    public function getBase58CheckAddress(string $addressBin): string
    {
        $hash0 = Hash::SHA256($addressBin);
        $hash1 = Hash::SHA256($hash0);
        $checksum = substr($hash1, 0, 4);
        $checksum = $addressBin . $checksum;

        return Base58::encode(Crypto::bin2bc($checksum));
    }

    public function generateAddress(): Address
    {
        $attempts = 0;
        $validAddress = false;

        do {
            if ($attempts++ === 5) {
                throw new TronErrorException('Could not generate valid key');
            }

            $keyPair = $this->genKeyPair();
            $privateKeyHex = $keyPair['private_key_hex'];
            $pubKeyHex = $keyPair['public_key'];

            //We cant use hex2bin unless the string length is even.
            if (strlen($pubKeyHex) % 2 !== 0) {
                continue;
            }

            $pubKeyBin = hex2bin($pubKeyHex);
            $addressHex = $this->getAddressHex($pubKeyBin);
            $addressBin = hex2bin($addressHex);
            $addressBase58 = $this->getBase58CheckAddress($addressBin);

            $address = new Address($addressBase58, $privateKeyHex, $addressHex);
            $validAddress = $this->validateAddress($address);

        } while (!$validAddress);

        return $address;
    }

    public function validateAddress(Address $address): bool
    {
        if (!$address->isValid()) {
            return false;
        }

        $body = $this->_api->post('/wallet/validateaddress', [
            'address' => $address->address,
        ]);

        return $body->result;
    }

    public function getAccount(Address $address): ?Account
    {
        $body = $this->_api->post('/wallet/getaccount', [
            'address' => $address->hexAddress,
        ]);

        if (isset($body->address)) {
            $address = new Address($body->address);
            return new Account($address, $body->balance, $body->create_time);
        }

        return null;
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
     * This is open to attacks. Instead use createTransaction(),
     * then sign it locally,
     * then broadcastTransaction()
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

    public function getTransactionById(string $transactionID): Transaction
    {
        $body = $this->_api->post('/wallet/gettransactionbyid', [
                'value' => $transactionID,
            ]
        );

        return new Transaction($body->txID, $body->raw_data);
    }

    public function getNowBlock(): Block
    {
        $body = $this->_api->post('/wallet/getnowblock');

        return new Block($body->blockID, $body->block_header);
    }

    public function getBlockById(string $blockId): Block
    {
        $body = $this->_api->post('/wallet/getblockbyid', [
                'value' => $blockId,
            ]
        );

        return new Block($body->blockID, $body->block_header);
    }

    public function listNodes(): array
    {
        return $this->_api->post('/wallet/listnodes', [], true);
    }

    public function listWitnesses(): array
    {
        $response = $this->_api->post('/wallet/listwitnesses', [], true);

        foreach ($response['witnesses'] as $witnessData) {
            $witnesses[] = new Witness(
                $witnessData['address'],
                $witnessData['url'],
                $witnessData['voteCount'],
                $witnessData['totalProduced'],
                $witnessData['latestBlockNum'],
                $witnessData['latestSlotNum']
            );
        }

        return $witnesses;
    }

//    public function freezeBalance(Address $ownerAddress, float $balanceToFreeze, int $durationDays, string $resource = 'BANDWIDTH')
//    {
//        $body = $this->_api->post('/wallet/freezebalance', [
//                'owner_address'   => $ownerAddress->hexAddress,
//                'frozen_balance'  => $balanceToFreeze,
//                'frozen_duration' => $durationDays,
//                'resource'        => $resource,
//            ]
//        );
//
//        return $body;
//    }
}
