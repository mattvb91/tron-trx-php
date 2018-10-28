<?php

use PHPUnit\Framework\TestCase;

class TransactionTest extends TestCase
{
    /**
     * @covers \mattvb91\TronTrx\Transaction::isSigned
     */
    public function testIsSigned()
    {
        $transaction = new \mattvb91\TronTrx\Transaction('', new stdClass());
        $this->assertFalse($transaction->isSigned());

        $transaction->signature = ['wdwd'];
        $this->assertTrue($transaction->isSigned());
    }
}
