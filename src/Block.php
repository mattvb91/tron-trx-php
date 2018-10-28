<?php

namespace mattvb91\TronTrx;

/**
 * Class Block
 * @package mattvb91\TronTrx
 */
class Block
{
    public $blockID;
    public $block_header;

    public function __construct(string $blockID, \stdClass $block_header)
    {
        if (!strlen($blockID)) {
            throw new \Exception('blockID empty');
        }

        $this->blockID = $blockID;
        $this->block_header = $block_header;
    }


}