<?php

namespace Iroge\SolanaPhpSdk\Tests\Unit\Programs;

use Iroge\SolanaPhpSdk\Exceptions\AccountNotFoundException;
use Iroge\SolanaPhpSdk\Programs\SystemProgram;
use Iroge\SolanaPhpSdk\SolanaRpcClient;
use Iroge\SolanaPhpSdk\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;


class MetaPlexProgramTest extends TestCase
{

    private SystemProgram $program;





    /**
     * @throws AccountNotFoundException
     */
    #[Test]
    public function test_it_getsProgramAccounts(): void
    {

        $client = $this->assembleClient('POST', []);

        $solana = new SystemProgram($client);

        $this->expectException(AccountNotFoundException::class);
        $solana->getAccountInfo('abc123');
    }



}
