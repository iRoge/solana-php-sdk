<?php

namespace Iroge\SolanaPhpSdk\Util;

interface HasSecretKey
{
    public function getSecretKey(): Buffer;
}
