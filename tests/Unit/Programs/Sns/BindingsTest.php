<?php
namespace Iroge\SolanaPhpSdk\Tests\Unit\Programs\SNS;

use Iroge\SolanaPhpSdk\Connection;
use Iroge\SolanaPhpSdk\Exceptions\AccountNotFoundException;
use Iroge\SolanaPhpSdk\Exceptions\InputValidationException;
use Iroge\SolanaPhpSdk\Exceptions\SNSError;
use Iroge\SolanaPhpSdk\Programs\SnsProgram;
use Iroge\SolanaPhpSdk\SolanaRpcClient;
use Iroge\SolanaPhpSdk\Tests\TestCase;
use Iroge\SolanaPhpSdk\PublicKey;
use Iroge\SolanaPhpSdk\TransactionInstruction;
use Iroge\SolanaPhpSdk\Util\Buffer;
use PHPUnit\Framework\MockObject\Exception;

class BindingsTest extends TestCase
{
    /**
     * @throws InputValidationException
     * @throws Exception
     */
    #[Test]
    public function testCreateSubDomainFast()
    {
        // Arrange

        $nameOwnerKey = new PublicKey('6V3DAZhWgATw8hrmMh7DnvLgaVpHLuMafZZPTVnyUs6Y');


        $rpcClient = new SolanaRpcClient('https://api.mainnet-beta.solana.com');
        $connection = new Connection($rpcClient);
        $sns = new SnsProgram($rpcClient);

        $instruction = $sns->createSubdomainFast(
                $connection,
                'subdomain.chongkan.sol',
                new PublicKey('57vj6H1omWUvrQypM8esx4q67WNRZhTW3ZHZ97unkSTb'), // f.chongkan.sol
                new PublicKey('34MxBdMJYgugd9ZzmZN338kL1vMqkhPqtnZG5qmWnfn1'),
                $nameOwnerKey,
                1_000,
                $nameOwnerKey
            );


        // Assert
        $this->assertInstanceOf(TransactionInstruction::class, $instruction[1][0]);
        // TODO Assert IX keys and data

    }

    #[Test]
    public function test_createNameRegistry()
    {
        // Arrange
        $nameOwnerSigner = new PublicKey(Buffer::alloc(32));

        $client = $this->createMock(SolanaRpcClient::class);
        $connection = $this->createMock(Connection::class);
        $sns = new SnsProgram($client);


        $instruction = $sns->createNameRegistry(
            $connection,
            'domain',
            2000,
            $nameOwnerSigner,
            $nameOwnerSigner, // could be someone else
            null, null, null
        );

        // Assert
        $this->assertInstanceOf(TransactionInstruction::class, $instruction);
        $this->assertEquals(0, $instruction->data->toArray()[0]);
    }


}
