<?php

namespace Iroge\SolanaPhpSdk\Programs\SplToken\Actions;

use Iroge\SolanaPhpSdk\Connection;
use Iroge\SolanaPhpSdk\Exceptions\AccountNotFoundException;
use Iroge\SolanaPhpSdk\Exceptions\GenericException;
use Iroge\SolanaPhpSdk\Exceptions\InputValidationException;
use Iroge\SolanaPhpSdk\Exceptions\InvalidIdResponseException;
use Iroge\SolanaPhpSdk\Exceptions\MethodNotFoundException;
use Iroge\SolanaPhpSdk\Exceptions\TokenInvalidAccountOwnerError;
use Iroge\SolanaPhpSdk\Exceptions\TokenInvalidMintError;
use Iroge\SolanaPhpSdk\Exceptions\TokenOwnerOffCurveError;
use Iroge\SolanaPhpSdk\Keypair;
use Iroge\SolanaPhpSdk\Programs\SplToken\State\Account;
use Iroge\SolanaPhpSdk\PublicKey;
use Iroge\SolanaPhpSdk\Transaction;
use Iroge\SolanaPhpSdk\Util\Commitment;
use Iroge\SolanaPhpSdk\Util\ConfirmOptions;
use Iroge\SolanaPhpSdk\Util\Signer;
use Exception;
use Psr\Http\Client\ClientExceptionInterface;
use function Iroge\SolanaPhpSdk\Programs\SplToken\getAccount;

trait SPLTokenActions {

    /**
     * @param Connection $connection
     * @param Signer|Keypair $payer
     * @param PublicKey $mint
     * @param PublicKey $owner
     * @param boolean $allowOwnerOffCurve
     * @param Commitment|null $commitment
     * @param ConfirmOptions $confirmOptions
     * @param PublicKey $programId
     * @param PublicKey $associatedTokenProgramId
     * @return mixed
     * @throws AccountNotFoundException
     * @throws ClientExceptionInterface
     * @throws InputValidationException
     * @throws TokenInvalidAccountOwnerError
     * @throws TokenInvalidMintError
     * @throws TokenOwnerOffCurveError
     * @throws GenericException
     * @throws InvalidIdResponseException
     * @throws MethodNotFoundException
     * @throws \SodiumException
     */
    public function getOrCreateAssociatedTokenAccount(
        Connection     $connection,
        mixed          $payer,
        PublicKey      $mint,
        PublicKey      $owner,
        bool           $allowOwnerOffCurve = true,
        Commitment     $commitment = null,
        ConfirmOptions $confirmOptions = null,
        PublicKey      $programId = new PublicKey(self::TOKEN_PROGRAM_ID),
        PublicKey      $associatedTokenProgramId = new PublicKey(self::ASSOCIATED_TOKEN_PROGRAM_ID)
    ): Account
    {

        $associatedToken = $this->getAssociatedTokenAddressSync(
            $mint,
            $owner,
            $allowOwnerOffCurve,
            $programId,
            $associatedTokenProgramId
        );
        $ata = $associatedToken->toBase58();
        try {
            $account = Account::getAccount($connection, $associatedToken, $commitment, $programId);
        } catch (Exception $error) {
            if ($error instanceof AccountNotFoundException || $error instanceof TokenInvalidAccountOwnerError) {
                try {
                    $transaction = new Transaction();
                    $transaction->add(
                        $this->createAssociatedTokenAccountInstruction(
                            $payer->getPublicKey(),
                            $associatedToken,
                            $owner,
                            $mint,
                            $programId,
                            $associatedTokenProgramId
                        )
                    );
                    if (!$confirmOptions) $confirmOptions = new ConfirmOptions();
                    $transaction->feePayer = $payer->getPublicKey();
                    $txnHash = $connection->sendTransaction( $transaction, [$payer]);
                } catch (Exception $error) {
                    // Ignore all errors
                    // Account Exists but is not funded
                    throw $error;
                }

                $account = Account::getAccount($connection, $associatedToken, $commitment, $programId);
            } else {
                throw $error;
            }
        }

        if ($account->mint != $mint) throw new TokenInvalidMintError(
            $account->mint->toBase58() . ' != ' . $mint->toBase58()
        );
        if ($account->owner != $owner) throw new TokenInvalidAccountOwnerError(
            $account->owner->toBase58() . ' != ' . $owner->toBase58()
        );

        return $account;
    }





}
