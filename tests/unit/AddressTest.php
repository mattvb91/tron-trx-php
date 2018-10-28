<?php

use mattvb91\TronTrx\Address;

class AddressTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers \mattvb91\TronTrx\Address::__construct
     */
    public function testThatInvalidAddressThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        new Address();
    }
}
