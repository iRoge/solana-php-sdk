<?php

namespace Iroge\SolanaPhpSdk\Programs;


use Iroge\SolanaPhpSdk\Exceptions\InputValidationException;
use Iroge\SolanaPhpSdk\Exceptions\TokenOwnerOffCurveError;
use Iroge\SolanaPhpSdk\Program;
use Iroge\SolanaPhpSdk\Programs\SplToken\Actions\SPLTokenActions;
use Iroge\SolanaPhpSdk\Programs\SplToken\Instructions\SPLTokenInstructions;
use Iroge\SolanaPhpSdk\PublicKey;


/**
 * @property $SOLANA_TOKEN_PROGRAM_ID
 */
class SplTokenProgram extends Program
{
    public const TOKEN_PROGRAM_ID = 'TokenkegQfeZyiNwAJbNbGKPFXCWuBvf9Ss623VQ5DA';
    public const NATIVE_MINT = 'So11111111111111111111111111111111111111112';
    public const ASSOCIATED_TOKEN_PROGRAM_ID = 'ATokenGPvbdGVxr1b2hvZbsiqW5xWH25efTNsLJA8knL';
    public const TOKEN_2022_PROGRAM_ID ='TokenzQdBNbLqP5VEhdkAS6EPFLC1PHnBqCXEpPxuEb';
    public const TOKEN_2022_MINT = '9pan9bMn5HatX4EJdBwg9VgCa7Uz5HL8N1m5D3NdXejP';

    use SPLTokenActions;
    use SPLTokenInstructions;

    /**
     * @param string $pubKey
     * @return mixed
     */
    public function getTokenAccountsByOwner(string $pubKey)
    {
        return $this->client->call('getTokenAccountsByOwner', [
            $pubKey,
            [
                'programId' => self::TOKEN_PROGRAM_ID,
            ],
            [
                'encoding' => 'jsonParsed',
            ],
        ]);
    }

    /**
     * @param PublicKey $mint
     * @param PublicKey $owner
     * @param bool $allowOwnerOffCurve
     * @param string|null $programId
     * @param null $associatedTokenProgramId
     * @return PublicKey
     * @throws TokenOwnerOffCurveError
     * @throws InputValidationException
     */
    public function getAssociatedTokenAddressSync(
        PublicKey $mint,
        PublicKey $owner,
        bool      $allowOwnerOffCurve = false,
        PublicKey    $programId = new PublicKey(self::TOKEN_PROGRAM_ID),
                  PublicKey $atPid = new PublicKey(self::ASSOCIATED_TOKEN_PROGRAM_ID)
    ): PublicKey {
        if (!$allowOwnerOffCurve && !PublicKey::isOnCurve($owner->toBinaryString())) {
            throw new TokenOwnerOffCurveError();
        }



        $address = PublicKey::findProgramAddressSync(
            [$owner->toBuffer(), $programId->toBuffer(), $mint->toBuffer()],
            $atPid
        );

        return $address[0];
    }




}
