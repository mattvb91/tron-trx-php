<?php

use mattvb91\TronTrx\Block;
use PHPUnit\Framework\TestCase;

class BlockTest extends TestCase
{

    /**
     * @covers \mattvb91\TronTrx\Block::__construct
     */
    public function testConstructorThrowsException()
    {
        new Block('blockId', new stdClass());

        $this->expectException(Exception::class);
        new Block('', new stdClass());
    }
}
