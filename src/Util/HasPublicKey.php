<?php

namespace Iroge\SolanaPhpSdk\Util;

use Iroge\SolanaPhpSdk\PublicKey;

interface HasPublicKey
{
    public function getPublicKey(): PublicKey;
}
