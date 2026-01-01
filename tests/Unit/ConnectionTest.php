<?php

namespace Iroge\SolanaPhpSdk\Tests\Unit;

use Iroge\SolanaPhpSdk\Connection;
use Iroge\SolanaPhpSdk\Exceptions\AccountNotFoundException;
use Iroge\SolanaPhpSdk\Exceptions\GenericException;
use Iroge\SolanaPhpSdk\Tests\TestCase;
use Iroge\SolanaPhpSdk\SolanaRpcClient;
use Iroge\SolanaPhpSdk\Transaction;
use Iroge\SolanaPhpSdk\Keypair;
use Iroge\SolanaPhpSdk\Programs\SystemProgram;
use PHPUnit\Framework\MockObject\Exception;
use SodiumException;


class ConnectionTest extends TestCase
{



    /**
     * @throws GenericException
     * @throws SodiumException
     */
    public function testSimulateTransaction()
    {
        $account1 = Keypair::generate();
        $account2 = Keypair::generate();
        $recentBlockhash = $account1->getPublicKey()->toBase58(); // Fake recentBlockhash

        $transfer1 = SystemProgram::transfer($account1->getPublicKey(), $account2->getPublicKey(), 123);
        $transfer2 = SystemProgram::transfer($account2->getPublicKey(), $account1->getPublicKey(), 123);

        $orgTransaction = new Transaction($recentBlockhash);
        $orgTransaction->add($transfer1, $transfer2);
        $orgTransaction->sign($account1, $account2);

        $newTransaction = new Transaction($orgTransaction->recentBlockhash, null, null, $orgTransaction->signatures);
        $newTransaction->add($transfer1, $transfer2);

        // TODO - Fix this test, call the method and compare the transactions

        $this->assertEquals($orgTransaction, $newTransaction);

    }

    #[Test]
    public function testGetBalance()
    {
        $pubKey = '3Wnd5Df69KitZfUoPYZU438eFRNwGHkhLnSAWL65PxJX';
        $balance = 100;

        $clientMock = $this->createMock(SolanaRpcClient::class);
        $clientMock->expects($this->once())
            ->method('call')
            ->with('getBalance', [$pubKey])
            ->willReturn(['value' => $balance]);

        $connection = new Connection($clientMock);

        $result = $connection->getBalance($pubKey);
        $this->assertEquals($balance, $result);
    }










}
