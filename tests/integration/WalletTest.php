<?php

use GuzzleHttp\Client;
use mattvb91\TronTrx\Address;
use mattvb91\TronTrx\Api;
use mattvb91\TronTrx\Transaction;
use PHPUnit\Framework\TestCase;

class WalletTest extends TestCase
{
    private $_api;

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->_api = new Api(new Client([
            'base_uri' => 'http://192.168.178.26:8090',
        ]));
    }

    /**
     * @covers \mattvb91\TronTrx\Wallet::__construct
     * @covers \mattvb91\TronTrx\Address::__construct
     * @covers \mattvb91\TronTrx\Wallet::generateAddress
     */
    public function testGenerateAddress()
    {
        $wallet = new mattvb91\TronTrx\Wallet($this->_api);

        $address = $wallet->generateAddress();
        $this->assertInstanceOf(Address::class, $address);

        return $address;
    }

    /**
     * @depends testGenerateAddress
     * @covers  \mattvb91\TronTrx\Wallet::validateAddress
     */
    public function testAddressValidation(Address $address)
    {
        $wallet = new mattvb91\TronTrx\Wallet($this->_api);

        $this->assertTrue($wallet->validateAddress($address));
    }

    /**
     * @covers \mattvb91\TronTrx\Wallet::easyTransferByPrivate
     * @return Address
     */
    public function testEasyTransferByPrivateThrowsException()
    {
        $this->expectException(Exception::class);

        $wallet = new \mattvb91\TronTrx\Wallet($this->_api);
        $address = $wallet->generateAddress();

        $this->assertTrue($wallet->easyTransferByPrivate(
            'B8BEAD956B259841440523B639970FA4F5D3B787720EC74E7A6155287222CC45',
            $address,
            1
        ));
    }


    /**
     * @covers \mattvb91\TronTrx\Wallet::getAccount
     * @covers \mattvb91\TronTrx\Account::__construct
     */
    public function testGetAccount()
    {
        $wallet = new \mattvb91\TronTrx\Wallet($this->_api);
        $address = $wallet->generateAddress();

        /**
         * Instantiate the fromAddress with private key that is generated
         * for us with the docker tron sample.
         *
         * This is required so we can then create a transaction with $fromAddress.
         */
        $this->_api->getClient()
            ->post('/wallet/easytransferbyprivate', [
                'json' => [
                    'privateKey' => 'B8BEAD956B259841440523B639970FA4F5D3B787720EC74E7A6155287222CC45',
                    'toAddress'  => $address->hexAddress,
                    'amount'     => 1,
                ],
            ]);

        $account = $wallet->getAccount($address);

        $this->assertInstanceOf(\mattvb91\TronTrx\Account::class, $account);
    }

    /**
     * @covers \mattvb91\TronTrx\Wallet::createTransaction
     * @covers \mattvb91\TronTrx\Transaction::__construct
     */
    public function testCreateTransaction()
    {
        $wallet = new \mattvb91\TronTrx\Wallet($this->_api);

        $toAddress = $wallet->generateAddress();
        $fromAddress = $wallet->generateAddress();

        /**
         * Instantiate the fromAddress with private key that is generated
         * for us with the docker tron sample.
         *
         * This is required so we can then create a transaction with $fromAddress.
         */
        $this->_api->getClient()
            ->post('/wallet/easytransferbyprivate', [
                'json' => [
                    'privateKey' => 'B8BEAD956B259841440523B639970FA4F5D3B787720EC74E7A6155287222CC45',
                    'toAddress'  => $fromAddress->hexAddress,
                    'amount'     => 1,
                ],
            ]);

        $transaction = $wallet->createTransaction($toAddress, $fromAddress, 1);
        $this->assertInstanceOf(Transaction::class, $transaction);

        return ['transaction' => $transaction, 'address' => $fromAddress];
    }

    /**
     * @covers \mattvb91\TronTrx\Wallet::signTransaction
     * @depends testCreateTransaction
     */
    public function testSignTransaction(array $input)
    {
        $wallet = new \mattvb91\TronTrx\Wallet($this->_api);

        /** @var Transaction $transaction */
        $transaction = $input['transaction'];

        $wallet->signTransaction($transaction, $input['address']->privateKey);

        $this->assertTrue($transaction->isSigned());
    }
}
