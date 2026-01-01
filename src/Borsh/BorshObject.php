<?php

namespace Iroge\SolanaPhpSdk\Borsh;

trait BorshObject
{
    use BorshDeserializable;
    use BorshSerializable;

    /**
     * @var array Holds dynamic properties
     */
    public $fields = [];


}
