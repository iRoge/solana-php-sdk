<?php

namespace Iroge\SolanaPhpSdk\Programs\SNS\State;

use Iroge\SolanaPhpSdk\Accounts\Did\VerificationMethodStruct;
use Iroge\SolanaPhpSdk\Accounts\Did\ServiceStruct;
use Iroge\SolanaPhpSdk\Accounts\DidData;
use Iroge\SolanaPhpSdk\Borsh\Borsh;
use Iroge\SolanaPhpSdk\Borsh\BorshDeserializable;
use Iroge\SolanaPhpSdk\Borsh\BorshObject;
use Iroge\SolanaPhpSdk\Connection;
use Iroge\SolanaPhpSdk\Exceptions\AccountNotFoundException;
use Iroge\SolanaPhpSdk\PublicKey;
use Iroge\SolanaPhpSdk\Exceptions\SNSError;
use Iroge\SolanaPhpSdk\Util\Buffer;


class NameRegistryStateAccount
{

    use BorshObject;

    public $data;


    public const SCHEMA = [
        self::class => [
            'kind' => 'struct',
            'fields' => [
                ['parentName', 'pubkey'],
                ['owner', 'pubkey'],
                ['class', 'pubkey']
            ],
        ],
    ];
    const SOL_RECORD_SIG_LEN = 96; // HEADER_LEN

    /**
     * @throws SNSError
     * @throws AccountNotFoundException
     */
    public static function retrieve(Connection $connection, string $nameAccountKey): array
    {
        $nameAccount = $connection->getAccountInfo($nameAccountKey);
        if (!$nameAccount) {
            throw new SNSError(SNSError::AccountDoesNotExist);
        }

        $base64String = base64_decode($nameAccount['data'][0]);
        $uint8Array = array_values(unpack('C*', $base64String));
        $dataBuffer = Buffer::from($base64String);

        $res = NameRegistryStateAccount::deserialize($dataBuffer->toArray());

        $res->data = $dataBuffer->slice(self::SOL_RECORD_SIG_LEN);
        // TODO: Implement retrieveNftOwner
        //$nftOwner = retrieveNftOwner($connection, $nameAccountKey);

        return ['registry' => $res, 'nftOwner' => false, 'nameAccountKey' => $nameAccountKey];
    }

    public static function deserialize(array $buffer): self
    {
        return Borsh::deserialize(self::SCHEMA, self::class, $buffer);
    }
}



